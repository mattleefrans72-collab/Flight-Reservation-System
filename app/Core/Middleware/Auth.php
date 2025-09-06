<?php

namespace App\Core\Middleware;

class Auth {
  public function handle() {
    if (! $_SESSION["user"] ?? false) {
      header("location: /");
      exit();
    }
  }
}

?>