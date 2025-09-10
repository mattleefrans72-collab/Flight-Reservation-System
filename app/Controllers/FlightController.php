<?php
namespace App\Controllers;

use App\Http\Forms\FlightForm;
use Amadeus\Amadeus;
use Amadeus\Exceptions\ResponseException;
use App\Core\Storage;
use App\Http\Session\Flight;
use App\Core\Config;

class FlightController {
  public function index() {
    $params = [
      "from" => $_GET["from"],
      "to" => $_GET["to"],
      "departure" => $_GET["departure"],
      "return" => $_GET["return"],
      "adults" => $_GET["adults"],
      "childrens" => $_GET["childrens"]
    ];
    FlightForm::validate($params); 

    if (!(Flight::getInfo() == $params) || !Storage::getWithExpiry("flights")) {

      Flight::storeInfo($params);

      // 2. Set API credentials
      $apiKey = Config::get('amadeus.api_key');
      $privateKey = Config::get('amadeus.api_secret');

      try {
        // 3. Build Amadeus client
        $amadeus = Amadeus::builder($apiKey, $privateKey)->build();

        // 4. Make API call
        $flightOffers = $amadeus->getShopping()->getFlightOffers()->get([
            'originLocationCode' => $_GET["from"],
            'destinationLocationCode' => $_GET["to"],
            'departureDate' => $_GET["departure"],
            'returnDate' => $_GET["return"],
            'adults' => $_GET["adults"],
            'max' => 100
        ]);

          // 5. Decode the JSON response
        $allFlights = json_decode($flightOffers[0]->getResponse()->getBody(), true) ?? [];

        Storage::storeWithExpiry("flights", $allFlights, 3600);

      } catch (ResponseException $e) {
          throw new \Exception('API error: ' . $e->getMessage());
      }
    }

    $allFlights = Storage::getWithExpiry("flights");
    $response = $this->paginate($allFlights);
    // 6. Load view with data
    view('flight.view.php', $response);
  }
  protected function paginate($response, $perPage = 15) {
    $data = $response['data'];
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $total = count($data);
    $totalPages = ceil($total / $perPage);
    $offset = ($page - 1) * $perPage;
    $pagedFlights = array_slice($data, $offset, $perPage);

    return [
        'flights' => $pagedFlights,
        'totalPages' => $totalPages,
        'currentPage' => $page,
        'allFlights' => $data,
        'response' => $response
    ];
  }
}