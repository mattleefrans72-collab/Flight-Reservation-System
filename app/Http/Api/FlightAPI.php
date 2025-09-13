<?php

namespace App\Http\Api;

use App\Core\Config;
use Amadeus\Amadeus;
use Amadeus\Exceptions\ResponseException;

class FlightAPI {
  protected static $apiKey;
  protected static $privateKey;
  public static function call($params) {
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
      $flights = json_decode($flightOffers[0]->getResponse()->getBody(), true) ?? [];

      return $flights;

    } catch (ResponseException $e) {
        throw new \Exception('API error: ' . $e->getMessage());
    }
  }
}