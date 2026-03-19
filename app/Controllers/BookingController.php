<?php

namespace App\Controllers;
use App\Http\Session\Flight;
use App\Core\App;
class BookingController {
  public function select () {
    // Decode incoming flight data
    $flightData = json_decode($_POST['flight_data'], true);

    if (!$flightData) {
        // Invalid form submission
        header('Location: /');
        exit;
    }

    // Store in session
    Flight::storeInfo($flightData['inbound'], $flightData['outbound'], $flightData['fareDetails'], $flightData['dictionaries']);

    // Redirect to booking page
    header('Location: /booking');
    exit;
  } 
  public function show () {
    $flight = Flight::getInfo();
    if (empty($flight)) {
      header("location: /");
      exit;
    }
    view('booking.view.php', ['flight' => $flight]);
  }
  public function store () {
     if (empty(Flight::getInfo())) {
        header('Location: /');
        exit;
    }

    $flight = Flight::getInfo();

    $db = App::resolve("Core\Database");

    // Generate a simple booking reference (e.g. random string)
    $ref = strtoupper(bin2hex(random_bytes(4)));

    // get user id
    $users_id = $db->query("SELECT id FROM users WHERE email = :email", ["email" => $_SESSION["user"]["email"]])->fetchColumn();

    // Insert into `bookings`
    $stmt = $db->query("INSERT INTO bookings (user_id, booking_reference) VALUES (:id, :ref)", ['id' => $users_id, 'ref' => $ref]);
    $bookingId = $db->conn->lastInsertId();

    // Save outbound segment
    $this->insertSegment($db, $bookingId, 'outbound', $flight['outbound']);

    // Save inbound segment (if exists)
    if (!empty($flight['inbound'])) {
        $this->insertSegment($db, $bookingId, 'inbound', $flight['inbound']);
    }

    // Clear session if needed
    unset($_SESSION['flight_info']);

    // Redirect to confirmation page
    header("Location: /flight/manage");
    exit;
  }
  private function insertSegment($db, $bookingId, $type, $segments){
    // Each "segment" is one flight leg
    foreach ($segments as $segment) {
      $departure = $segment['departure'];
      $arrival   = $segment['arrival'];

      $stmt = $db->query("
          INSERT INTO booking_segments 
              (booking_id, segment_type, departure_code, arrival_code, departure_time, arrival_time, airline, aircraft, cabin, checked_bag, cabin_bag) 
          VALUES 
              (:booking_id, :segment_type, :departure_code, :arrival_code, :departure_time, :arrival_time, :airline, :aircraft, :cabin, :checked_bag, :cabin_bag)
      ", [
          'booking_id'    => $bookingId,
          'segment_type'  => $type, // 'outbound' or 'inbound'
          'departure_code'=> $departure['iataCode'],
          'arrival_code'  => $arrival['iataCode'],
          'departure_time'=> $departure['at'],
          'arrival_time'  => $arrival['at'],
          'airline'       => $segment['carrierCode'] ?? 'N/A',
          'aircraft'      => $segment['aircraft']['code'] ?? 'N/A',
          'cabin'         => $segment['cabin'] ?? 'ECONOMY',
          'checked_bag'   => $segment['checked_bag'] ?? 0,
          'cabin_bag'     => $segment['cabin_bag'] ?? 0,
      ]);
    }
  }
}