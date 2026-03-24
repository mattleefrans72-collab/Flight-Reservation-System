<?php

namespace App\Controllers;
use App\Core\Session\FlightSession;
use App\Services\BookingService;
class BookingController {
  public function select() {
    // Decode incoming flight data
    try {
      $flightData = json_decode($_POST["flight_data"], true);
    } catch (\Exception $e) {
      dd("Invalid flight data");
    }
    
    // Store in session
    FlightSession::storeInfo(
      $flightData["inbound"],
      $flightData["outbound"],
      $flightData["fareDetails"],
      $flightData["dictionaries"],
    );

    // Redirect to booking page
    header("Location: /booking");
    exit();
  }
  public function show() {
    $flight = FlightSession::getInfo();
    if (empty($flight)) {
      header("location: /");
      exit();
    }
    view("booking.view.php", ["flight" => $flight]);
  }
  public function store() {
    $service = new BookingService();

    try {
      $service->createBooking($_SESSION["user"]["id"]);

      header("Location: /flight/manage");
      exit();
    } catch (\Exception $e) {
      dd($e->getMessage());
      header("Location: /booking");
      exit();
    }
  }
}
