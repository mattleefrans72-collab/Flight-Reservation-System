<?php

namespace App\Controllers;

use App\Services\ManageBookingService;

class ManageController {
  public function index() {
    $service = new ManageBookingService();

    $bookings = $service->getUserBookings($_SESSION["user"]["id"]);

    view("manage.view.php", [
      "bookings" => $bookings,
    ]);
  }
  public function destroy() {
    $bookingId = (int) ($_POST["booking_id"] ?? 0);
    $userId = (int) $_SESSION["user"]["id"];

    $service = new ManageBookingService();

    $deleted = $service->deleteUserBooking($bookingId, $_SESSION["user"]["id"]);

    if (!$deleted) {
      dd("Failed to delete booking");
    }

    redirect("/flight/manage");
  }
}
