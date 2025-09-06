<?php

namespace App\Models\Airport;

class Airport {
  public $name;
  public $iata_code;
  public $municipality;
  public $region;
  public $country;

  public function __construct(
    $name,
    $iata_code,
    $municipality,
    $region,
    $country
  ) {
    $this->name = $name;
    $this->iata_code = $iata_code;
    $this->municipality = $municipality;
    $this->region = $region;
    $this->country = $country;
  }
}