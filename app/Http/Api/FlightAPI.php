<?php

namespace App\Http\Api;

use App\Core\Config;
use App\Http\Database\Airport_db;
use Amadeus\Amadeus;
use Amadeus\Exceptions\ResponseException;

class FlightAPI {
  protected static $apiKey;
  protected static $privateKey;
  public $response = [];
  public $meta = [];

  public function __construct($params) {
    try {
      
      if (!self::$apiKey) {
        self::$apiKey = Config::get('amadeus.api_key');
      }

      if (!self::$privateKey) {
          self::$privateKey = Config::get('amadeus.api_secret');
      }
      // 3. Build Amadeus client
      $amadeus = Amadeus::builder(self::$apiKey, self::$privateKey)->build();

      // 4. Make API call
      $flightOffers = $amadeus->getShopping()->getFlightOffers()->get($params);

        // 5. Decode the JSON response
      $response = json_decode($flightOffers[0]->getResponse()->getBody(), true) ?? [];

      $this->response = $response;

      $this->addFlightMetadata();

      $this->generateMetadata();

    } catch (ResponseException $e) {
        throw new \Exception('API error: ' . $e->getMessage());
    } catch (\TypeError $e) {
    throw new \Exception("Type error: " . $e->getMessage());
    } catch (\Exception $e) {
        throw new \Exception("Unexpected error: " . $e->getMessage());
    }
  }
  public function addFlightMetadata() {

    foreach ($this->response['data'] as &$flight) {
      // Get the last segment of the inbound itinerary
      $outboundSegments = $flight['itineraries'][0]['segments'] ?? [];
      $inboundSegments = $flight['itineraries'][1]['segments'] ?? [];
      $lastSegment = end($outboundSegments);

      $iataCode = $lastSegment['arrival']['iataCode'] ?? '';

      // Look up the country
      $country = Airport_db::country($iataCode);
      $flight['arrivalCountry'] = $country ?? 'Unknown';
      if ((stopLabel($outboundSegments) == 0) && (stopLabel($inboundSegments) == 0)) {
        $flight['direct'] = true;
      } else {
        $flight['direct'] = false;
      }
    }
  }
  public function generateMetadata() {
    $lowestPrice = null;
    $totalFlights = count($this->response['data']);
    $directCount = 0;
    $driectLowestPrice = null;
    $airlinesCount = [];

    foreach ($this->response['data'] as $flight) {
      $price = (float) ($flight['price']['total'] ?? 0);

      // Track lowest price
      if ($lowestPrice === null || $price < $lowestPrice) {
          $lowestPrice = $price;
      }

      if (($driectLowestPrice === null || $price < $lowestPrice) && (!empty($flight['direct']))) {
          $driectLowestPrice = $price;
      }

      // Count direct flights
      if (!empty($flight['direct'])) {
          $directCount++;
      }
    }
    
      $airlinesCount = $this->countAirlines($this->response['data']);

    
    $this->meta = [
        'lowestPrice' => $lowestPrice,
        'totalFlights' => $totalFlights,
        'directCount' => $directCount,
        'directLowestPrice' => $driectLowestPrice,
        'airlinesCount' => $airlinesCount
    ];
}
  public function filter($filter = []) {

    if (!empty($filter)) {
        $flights = array_filter($this->response['data'], function ($flight) use ($filter) {
            if (!empty($filter['stops']) && $filter['stops'] == 'direct') {
                if (empty($flight['direct'])) return false;
            }

            return true;
        });

        $this->response['data'] = array_values($flights);
    }
    // 1. Extract used carrier codes from remaining flight
    $usedCarrierCodes = [];

    foreach ($this->response['data'] as $flight) {
        foreach ($flight['itineraries'] as $itinerary) {
            foreach ($itinerary['segments'] as $segment) {
                if (!empty($segment['carrierCode'])) {
                    $usedCarrierCodes[$segment['carrierCode']] = true;
                }
            }
        }
    }

    // 2. Filter original carrier list
    $allCarriers = $this->response['dictionaries']['original_carriers'] ?? $this->response['dictionaries']['carriers'];
    $filteredCarriers = [];

    foreach ($usedCarrierCodes as $code => $_) {
        if (isset($allCarriers[$code])) {
            $filteredCarriers[$code] = $allCarriers[$code];
        }
    }

    // 3. Save filtered carriers back
    $this->response['dictionaries']['original_carriers'] = $allCarriers;
    $this->response['dictionaries']['carriers'] = $filteredCarriers;


    $airlinesCount = $this->countAirlines($this->response['data']);

    $this->meta['airlinesCount'] = $airlinesCount;
    }

    public function countAirlines($flights) {
      foreach ($flights as $flight) {
        foreach ($flight['itineraries'] as $itinerary) {
          foreach ($itinerary['segments'] as $segment) {
            $carrierCode = $segment['carrierCode'] ?? null;

            if ($carrierCode) {
                if (!isset($airlinesCount[$carrierCode])) {
                    $airlinesCount[$carrierCode] = 0;
                }

                $airlinesCount[$carrierCode]++;
            }
          }
        }
      }
      return $airlinesCount;
    }

}