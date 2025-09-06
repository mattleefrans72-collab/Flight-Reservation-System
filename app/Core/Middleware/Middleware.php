<?php

namespace App\Core\Middleware;

class Middleware {
  public const Map = [
    "guest" => Guest::class,
    "auth" => Auth::class
  ];
  public static function resolve($key) {

    if (!$key) {
      return;
    }

    $middleware = Middleware::Map[$key];

    if (!$middleware) {
      throw new \Exception("No matching middleware found for key '{$key}'.");
    }

    (new $middleware)->handle();   
  }
}

?>