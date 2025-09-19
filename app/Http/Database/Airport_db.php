<?php

namespace App\Http\Database;
use App\Core\App;
class Airport_db {
  
  public static function country($code) {
    $db = App::resolve('Core\Database');
    $row = $db->query("SELECT country FROM airport_summary
                WHERE iata_code = ?", [$code])->fetch();
    return $row['country'];
  }
}