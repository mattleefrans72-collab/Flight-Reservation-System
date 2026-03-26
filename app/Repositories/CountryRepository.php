<?php
namespace App\Repositories;
use App\Core\App;
class CountryRepository {
  protected $db;

  public function __construct() {
    $this->db = App::resolve('Core\Database');
  }

  public function getCountryByIata($code) {
    $row = $this->db
      ->query("SELECT country FROM airport_summary WHERE iata_code = :code", [
        "code" => $code,
      ])
      ->fetch();

    return $row["country"] ?? null;
  }
}
