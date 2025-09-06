<?php
return [
  // Database configuration
  'database' => [

    'airports' => [
      'host'     => 'localhost',
      'port'     => 3306,
      'dbname'   => 'csv_db 7',
      'charset'  => 'utf8mb4'
    ],
  ],

  // Amadeus API keys
  'amadeus' => [
      'api_key'    => 'UGI1MLRQZdpf2gDdhAemPeWiaxuieTGg',
      'api_secret' => 'qD0r3PRW3TZqjOQx',
  ],

    // You can add more config groups (e.g., mail, auth)
];
?>