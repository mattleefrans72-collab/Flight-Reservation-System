<?php

namespace App\Repositories;
use App\Core\App;
class CountryRepository {

  public static function getCountryByIata(string $code) {
    $row = App::resolve('Core\Database')->query(
      "SELECT country FROM airport_summary WHERE iata_code = :code", [
        "code" => $code,
      ])
      ->fetch();

    return $row["country"] ?? null;
  }
}
