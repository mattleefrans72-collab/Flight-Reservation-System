<?php
namespace App\Controllers;

use App\Services\FlightSearchService;

class FlightController {
  public function index() {
    $service = new FlightSearchService();
    $result = $service->search($_GET);

    view("flight.view.php", $result);
  }
}
