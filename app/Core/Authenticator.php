<?php

namespace App\Core;

class Authenticator {
  public function attemp ($email, $password) {
    $db = App::resolve("Core\Database");

    $user = $db->query("SELECT * FROM users WHERE email = :email", ["email" => $email])->fetch();


    if ($user) {
      if (password_verify($password, $user["password"])) {
        Authenticator::login($user);
        return true;
      }  
    }
    return false; 
  }
  public static function login($user) {
    $_SESSION["user"] = [
    "id" => $user["id"],
    "email" => $user["email"]
    ];  
    session_regenerate_id(true);
}
  public static function logout() {
    Session::destroy();
  }
}

?>