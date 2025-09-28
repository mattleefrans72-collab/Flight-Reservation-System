<?php 

namespace App\Controllers;

use App\Core\App;

class ManageController {
  protected $user_id;
  protected $db;
  public function __construct() {
    // get logged-in user id
    $db = App::resolve("Core\Database");
    $user_id = $db->query(
      "SELECT id FROM users WHERE email = :email",
      ["email" => $_SESSION["user"]["email"]]
    )->fetchColumn();
    $this->user_id = $user_id;
    $this->db = $db;
  }
  public function index() {
    // get all bookings for this user
    $bookings = $this->db->query(
        "SELECT * FROM bookings WHERE user_id = :id",
        ["id" => $this->user_id]
    )->fetchAll();

    // attach segments to each booking
    foreach ($bookings as &$booking) {
        $booking['segments'] = $this->db->query(
            "SELECT * FROM booking_segments WHERE booking_id = :id ORDER BY departure_time",
            ["id" => $booking['id']]
        )->fetchAll();
    }

    view("manage.view.php", [
        "bookings" => $bookings
    ]);
  }
  public function destroy () {
    $bookingId = $_POST['booking_id'];
    // verify this booking belongs to the logged-in user
    $booking = $this->db->query(
      "SELECT * FROM bookings WHERE id = :id AND user_id = :user_id",
      ["id" => $bookingId, "user_id" => $this->user_id]
    )->fetch();

    if ($booking) {
      // delete all related segments first
      $this->db->query("DELETE FROM booking_segments WHERE booking_id = ?", [$bookingId]);

      // delete the booking
      $this->db->query("DELETE FROM bookings WHERE id = ?", [$bookingId]);
    }

    // redirect back to manage page
    header("Location: /flight/manage");
    exit;
  }
}