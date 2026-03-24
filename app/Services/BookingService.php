<?php

namespace App\Services;

use App\Core\App;
use App\Core\Session\FlightSession;
use App\Core\Database;

class BookingService {
  protected $db;
  public function __construct() {
    $this->db = App::resolve("Core\Database");
  }
  public function createBookingFromSessionFlight($userId) {
    $flight = FlightSession::getInfo();

    if (empty($flight)) {
      throw new \Exception("No selected flight in session.");
    }

    $bookingReference = strtoupper(bin2hex(random_bytes(4)));

    $this->db->query(
      "INSERT INTO bookings (user_id, booking_reference) VALUES (:user_id, :booking_reference)",
      [
        "user_id" => $userId,
        "booking_reference" => $bookingReference,
      ],
    );

    $bookingId = $this->db->conn->lastInsertId();

    $this->insertSegments($bookingId, "outbound", $flight["outbound"] ?? []);

    if (!empty($flight["inbound"])) {
      $this->insertSegments($bookingId, "inbound", $flight["inbound"]);
    }

    FlightSession::clear();

    return $bookingReference;
  }
  protected function insertSegments($bookingId, $segmentType, $segments) {
    foreach ($segments as $segment) {
      $this->db->query(
        "INSERT INTO booking_segments (booking_id, segment_type, departure_code, arrival_code, departure_time, arrival_time, airline, aircraft, cabin, checked_bag, cabin_bag)
        VALUES (:booking_id, :segment_type, :departure_code, :arrival_code, :departure_time, :arrival_time, :airline, :aircraft, :cabin, :checked_bag, :cabin_bag)",
        [
          "booking_id" => $bookingId,
          "segment_type" => $segmentType,
          "departure_code" => $departure["iataCode"] ?? null,
          "arrival_code" => $arrival["iataCode"] ?? null,
          "departure_time" => $departure["at"] ?? null,
          "arrival_time" => $arrival["at"] ?? null,
          "airline" => $segment["carrierCode"] ?? "N/A",
          "aircraft" => $segment["aircraft"]["code"] ?? "N/A",
          "cabin" => $segment["cabin"] ?? "ECONOMY",
          "checked_bag" => $segment["checked_bag"] ?? 0,
          "cabin_bag" => $segment["cabin_bag"] ?? 0,
        ],
      );
    }
  }
  public function createBooking($userId) {
    $flight = FlightSession::getInfo();

    if (empty($flight)) {
      throw new \Exception("No selected flight in session.");
    }

    $bookingReference = strtoupper(bin2hex(random_bytes(4)));

    $this->db->query(
      "INSERT INTO bookings (user_id, booking_reference)
             VALUES (:user_id, :booking_reference)",
      [
        "user_id" => $userId,
        "booking_reference" => $bookingReference,
      ],
    );

    $bookingId = $this->db->conn->lastInsertId();

    $this->insertSegments($bookingId, "outbound", $flight["outbound"] ?? []);

    if (!empty($flight["inbound"])) {
      $this->insertSegments($bookingId, "inbound", $flight["inbound"]);
    }

    FlightSession::clear();

    return $bookingReference;
  }

  public function store() {
    $service = new BookingService();

    try {
      $bookingReference = $service->createBooking($_SESSION["user"]["id"]);

      // (optional) store success message
      $_SESSION["success"] = "Booking created: " . $bookingReference;

      // 4. Redirect after success
      header("Location: /flight/manage");
      exit();
    } catch (\Exception $e) {
      // 5. Handle failure
      $_SESSION["error"] = $e->getMessage();

      header("Location: /booking");
      exit();
    }
  }
}
