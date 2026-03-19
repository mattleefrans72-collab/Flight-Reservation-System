<?php
namespace App\Controllers;

use App\Http\Forms\FlightForm;
use App\Core\Cache;
use App\Http\Api\FlightAPI;

class FlightController {
  protected $resetPageNum = false;

  public function index() {

    $params = [
      'originLocationCode' => $_GET["from"] ?? '',
      'destinationLocationCode' => $_GET["to"] ?? '',
      'departureDate' => $_GET["departure"] ?? '',
      'returnDate' => $_GET["return"] ?? '',
      'adults' => $_GET["adults"] ?? '',
      'children' => is_numeric($_GET["children"] ?? '') ? (int)$_GET["children"] : 0,
      'max' => 100
    ];
    
    if (isset($_GET['class']) && $_GET['class'] !== 'ANY') {
      $params['travelClass'] = $_GET['class'];
    }
    if (empty($_GET)) {
      $params = $_SESSION['params'] ?? '';
    }
    
    $_SESSION['params'] = $params;

    $stops = $_GET['stops'] ?? '';
    $airlines = $_GET['airlines_show'] ?? [];

    FlightForm::validate($params); 

    $cache = new Cache();
    $filter = [];
    $encode = $params;
    $encode['stops'] = $stops;
    $filter['stops'] = $stops;
    $encode['airlines'] = $airlines;
    $filter['airlines'] = $airlines;

    $key = $cache->encode($encode);
    // $key = 'flights_' . md5(json_encode($encode));
    
    $cached = $cache->get($key);
    $response = $cached['response'] ?? [];
    $meta = $cached['extraMeta'] ?? [];

    if (!$cached) {

      $this->resetPageNum = true;

      $flights_db = new FlightAPI($params);

      $flights_db->filter($filter);

      $response = $flights_db->response;

      $meta = $flights_db->meta;

      $cache->set($key, ['response' => $response, 'extraMeta' => $meta]);
    }



    $response = $this->paginate($response, $this->resetPageNum);

    // 6. Load view with data + only stop cache 
    unset($encode['airlines']);
    $stopKey = $cache->encode($encode);
    $stopCache = $cache->get($stopKey);

    view('flight.view.php',  ['response' => $response, 'extraMeta' => $meta, 'original_cache' => $stopCache]);
  }
  protected function paginate($response, $reset = false, $perPage = 15) {
    $data = $response['data'] ?? [];
    $page = !$reset ? (int)($_GET['page'] ?? 1) : 1;
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