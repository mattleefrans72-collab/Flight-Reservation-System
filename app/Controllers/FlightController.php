<?php
namespace App\Controllers;

use App\Http\Forms\FlightForm;
use App\Core\Cache;
use App\Http\Api\FlightAPI;

class FlightController {
  protected $resetPageNum = false;
  public function index() {
    $params = [
      'originLocationCode' => $_GET["from"],
      'destinationLocationCode' => $_GET["to"],
      'departureDate' => $_GET["departure"],
      'returnDate' => $_GET["return"],
      'adults' => $_GET["adults"],
      'children' => is_numeric($_GET["children"]) ? (int)$_GET["children"] : 0,
      'max' => 100
    ];
    if (isset($_GET['class']) && $_GET['class'] !== 'ANY') {
      $params['travelClass'] = $_GET['class'];
    }

    FlightForm::validate($params); 

    $cache = new Cache();
    $key = 'flights_' . md5(json_encode($params));

    $flights = $cache->get($key);

    if (!$flights) {

      $this->resetPageNum = true;

      $flights = FlightAPI::call($params);

      $cache->set($key, $flights);
    }

    $response = $this->paginate($flights, $this->resetPageNum);
    // 6. Load view with data
    view('flight.view.php', $response);
  }
  protected function paginate($response, $reset = false, $perPage = 15) {
    $data = $response['data'];
    $page = $reset ? 1 : (int)$_GET['page'];
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