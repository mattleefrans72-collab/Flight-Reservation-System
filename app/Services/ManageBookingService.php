<?php

namespace App\Services;

use App\Core\App;

class ManageBookingService {
  protected $db;
  public function __construct() {
    $this->db = App::resolve("Core\Database");
  }

  public function getUserBookings($userId) {
    
    $bookings = $this->db
      ->query(
        "SELECT * FROM bookings WHERE user_id = :user_id ORDER BY id DESC",
        ["user_id" => $userId],
      )
      ->fetchAll();

    if (!$bookings) {
      return [];
    }

    foreach ($bookings as &$booking) {
      $booking["segments"] = $this->db
        ->query(
          "SELECT * FROM booking_segments WHERE booking_id = :booking_id",
          ["booking_id" => $booking["id"]],
        )
        ->fetchAll();
    }

    return $bookings;
  }
  public function deleteUserBooking($bookingId, $userId) {
    $booking = $this->db
      ->query("SELECT id FROM bookings WHERE id = :id AND user_id = :user_id", [
        "id" => $bookingId,
        "user_id" => $userId,
      ])
      ->fetch();

    if (!$booking) {
      return false;
    }

    //for data integrity as it needs to delete all or not

    $this->db->conn->beginTransaction();

    try {
      $this->db->query(
        "DELETE FROM booking_segments WHERE booking_id = :booking_id",
        ["booking_id" => $bookingId],
      );

      $this->db->query("DELETE FROM bookings WHERE id = :id", [
        "id" => $bookingId,
      ]);

      $this->db->conn->commit();

      return true;
    } catch (\Throwable $e) {
      $this->db->conn->rollBack();
      throw $e;
    }
  }
}
