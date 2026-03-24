<?php

namespace App\Repositories;

use App\Core\Config;
use App\Repositories\CountryRepository;
use Amadeus\Amadeus;
use Amadeus\Exceptions\ResponseException;

class AirportRepository {
  protected static $apiKey;
  protected static $privateKey;
  public $response = [];
  public $meta = [];

  public function __construct($params) {
    try {
      if (!self::$apiKey) {
        self::$apiKey = Config::get("amadeus.api_key");
      }

      if (!self::$privateKey) {
        self::$privateKey = Config::get("amadeus.api_secret");
      }
      // 3. Build Amadeus client
      $amadeus = Amadeus::builder(self::$apiKey, self::$privateKey)->build();

      // 4. Make API call
      $flightOffers = $amadeus->getShopping()->getFlightOffers()->get($params);

      if (!empty($flightOffers)) {
        // 5. Decode the JSON response
        $response = json_decode(
          $flightOffers[0]->getResponse()->getBody(),
          true,
        );
        $this->response = $response;

        $this->addFlightMetadata();
        $this->generateMetadata();
      }
    } catch (ResponseException $e) {
      throw new \Exception("API error: " . $e->getMessage());
    }
  }

  public function addFlightMetadata() {
    foreach ($this->response["data"] as &$flight) {
      // Get the last segment of the inbound itinerary
      $outboundSegments = $flight["itineraries"][0]["segments"] ?? [];
      $inboundSegments = $flight["itineraries"][1]["segments"] ?? [];
      $lastSegment = end($outboundSegments);

      $iataCode = $lastSegment["arrival"]["iataCode"] ?? "";

      // Look up the country
      $country = CountryRepository::getCountryByIata($iataCode);
      $flight["arrivalCountry"] = $country ?? "Unknown";
      if (
        stopLabel($outboundSegments) == 0 &&
        stopLabel($inboundSegments) == 0
      ) {
        $flight["direct"] = true;
      } else {
        $flight["direct"] = false;
      }
    }
  }
  public function generateMetadata() {
    $lowestPrice = null;
    $totalFlights = count($this->response["data"]);
    $directCount = 0;
    $driectLowestPrice = null;
    $airlinesCount = [];

    foreach ($this->response["data"] as $flight) {
      $price = (float) ($flight["price"]["total"] ?? 0);

      // Track lowest price
      if ($lowestPrice === null || $price < $lowestPrice) {
        $lowestPrice = $price;
      }

      if (
        ($driectLowestPrice === null || $price < $lowestPrice) &&
        !empty($flight["direct"])
      ) {
        $driectLowestPrice = $price;
      }

      // Count direct flights
      if (!empty($flight["direct"])) {
        $directCount++;
      }
    }

    $airlinesCount = $this->countAirlines($this->response["data"]);

    $this->meta = [
      "lowestPrice" => $lowestPrice,
      "totalFlights" => $totalFlights,
      "directCount" => $directCount,
      "directLowestPrice" => $driectLowestPrice,
      "airlinesCount" => $airlinesCount,
    ];
  }
  public function filter($filter = []) {
    if (empty($this->response)) {
      return;
    }
    if (!empty($filter)) {
      $flights = array_filter($this->response["data"], function ($flight) use (
        $filter,
      ) {
        // Filter for direct flights
        if (!empty($filter["stops"]) && $filter["stops"] === "direct") {
          if (empty($flight["direct"])) {
            return false;
          }
        }

        // Filter for airlines
        if (!empty($filter["airlines"]) && is_array($filter["airlines"])) {
          $foundAirline = false;

          foreach ($flight["itineraries"] as $itinerary) {
            foreach ($itinerary["segments"] as $segment) {
              if (in_array($segment["carrierCode"], $filter["airlines"])) {
                $foundAirline = true;
                break 2; // Found a matching airline, stop checking
              }
            }
          }

          if (!$foundAirline) {
            return false; // no matching airline found, skip flight
          }
        }

        return true;
      });

      $this->response["data"] = array_values($flights);
    }
    // 1. Extract used carrier codes from remaining flight
    $usedCarrierCodes = [];

    foreach ($this->response["data"] as $flight) {
      foreach ($flight["itineraries"] as $itinerary) {
        foreach ($itinerary["segments"] as $segment) {
          if (!empty($segment["carrierCode"])) {
            $usedCarrierCodes[$segment["carrierCode"]] = true;
          }
        }
      }
    }

    // 2. Filter original carrier list
    $allCarriers =
      $this->response["dictionaries"]["original_carriers"] ??
      $this->response["dictionaries"]["carriers"];
    $filteredCarriers = [];

    foreach ($usedCarrierCodes as $code => $_) {
      if (isset($allCarriers[$code])) {
        $filteredCarriers[$code] = $allCarriers[$code];
      }
    }

    // 3. Save filtered carriers back
    $this->response["dictionaries"]["original_carriers"] = $allCarriers;
    $this->response["dictionaries"]["carriers"] = $filteredCarriers;

    $airlinesCount = $this->countAirlines($this->response["data"], $filteredCarriers);

    $this->meta["airlinesCount"] = $airlinesCount;
  }

  public function countAirlines($flights, $allcarriers = []) {
    $airlinesCount = [];
    foreach ($allcarriers as $carrierCode => $carrierName) {
      $airlinesCount[$carrierCode] = 0;
      foreach ($flights as $flight) {
        foreach ($flight["itineraries"] as $itinerary) {
          foreach ($itinerary["segments"] as $segment) {
            if ($segment["carrierCode"] === $carrierCode) {
              $airlinesCount[$carrierCode]++;
              break; // Found a matching segment, stop checking this flight
            }
          }
          break; // Stop checking other itineraries for this flight
        }
      }
    }
    return $airlinesCount;
  }
}
