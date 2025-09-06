<?php

namespace App\Http\Session;

class Flight {
  public static function storeInfo($info) {
    $_SESSION["flight_info"] = $info;
  }
  public static function getInfo() {
    return $_SESSION["flight_info"] ?? null;
  }
}
