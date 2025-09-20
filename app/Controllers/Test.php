<?php 
namespace App\Controllers;

use Amadeus\Amadeus;
use Amadeus\Exceptions\ResponseException;

class Test {
    public function index(): void {
try {
    $amadeus = Amadeus::builder("REPLACE_BY_YOUR_API_KEY", "REPLACE_BY_YOUR_API_SECRET")
        ->build();

    // Flight Offers Search GET
    $flightOffers = $amadeus->getShopping()->getFlightOffers()->get(
                        array(
                            "originLocationCode" => "PAR",
                            "destinationLocationCode" => "MAD",
                            "departureDate" => "2026-08-29",
                            "adults" => 1
                        )
                    );
    print $flightOffers[0];

    // Flight Offers Search POST
    $body ='{
              "originDestinations": [
                {
                  "id": "1",
                  "originLocationCode": "PAR",
                  "destinationLocationCode": "MAD",
                  "departureDateTimeRange": {
                    "date": "2023-08-29"
                  }
                }
              ],
              "travelers": [
                {
                  "id": "1",
                  "travelerType": "ADULT"
                }
              ],
              "sources": [
                "GDS"
              ]
            }';
    $flightOffers = $amadeus->getShopping()->getFlightOffers()->post($body);
    print $flightOffers[0];
} catch (ResponseException $e) {
    print $e;
}
}
}