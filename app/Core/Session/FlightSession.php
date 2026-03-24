<?php

namespace App\Core\Session;

class FlightSession {
  public static function storeInfo($inbound, $outbound, $fareDetails, $dictionaries) {
    $_SESSION["flight_info"] = ['inbound' => $inbound, 'outbound' => $outbound, 'fareDetails' => $fareDetails, 'dictionaries' => $dictionaries];
  }
  public static function getInfo($key = '') {
    return $_SESSION["flight_info"][$key] ?? $_SESSION["flight_info"];
  }
  public static function clear() {
    unset($_SESSION["flight_info"]);
  }
}
