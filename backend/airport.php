<?php

  
  header('Content-Type: application/json');
  include("database.php");

  class Airports {
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

    $search = $_GET['search'] ?? '';

    $sql =  "SELECT * FROM airport_summary
    WHERE name LIKE '%{$search}%'
    OR iata_code LIKE '%{$search}%'
    OR municipality LIKE '%{$search}%'
    OR region LIKE '%{$search}%'
    OR country LIKE '%{$search}%'";

    $result = mysqli_query($conn, $sql);

    $airports = [];
    $i = 0;

    while ($row = mysqli_fetch_array($result)) {
      $airports[$i] = new Airports(
        name: $row['name'],
        iata_code: $row['iata_code'],
        municipality: $row['municipality'],
        region: $row['region'],
        country: $row['country']
      );
      $i++;
    }

    echo json_encode($airports);
    
    mysqli_close($conn);
?>