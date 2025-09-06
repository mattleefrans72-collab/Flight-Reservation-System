<?php
declare(strict_types=1);
header('Content-Type: application/json');


use Amadeus\Amadeus;
use Amadeus\Exceptions\ResponseException;

require __DIR__ . '/../vendor/autoload.php'; 

getFlight();
function getFlight() {
  $apiKey = 'UGI1MLRQZdpf2gDdhAemPeWiaxuieTGg';
  $privateKey = 'qD0r3PRW3TZqjOQx';
  
  try {
      $amadeus = Amadeus::builder($apiKey, $privateKey)->build();

    // Flight Offers Search GET
    $flightOffers = $amadeus->getShopping()->getFlightOffers()->get(
        array(
            "originLocationCode" => "PAR",
            "destinationLocationCode" => "MAD",
            "departureDate" => "2025-09-29",
            "adults" => 1
        )
    );
  $data = json_decode($flightOffers[0]->getResponse()->getBody(), true);
  echo json_encode($data); 
                      
  } catch (ResponseException $e) {
    print $e;
  }
};
?>