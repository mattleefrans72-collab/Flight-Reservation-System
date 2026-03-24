<?php
namespace App\Controllers;

use App\Core\Authenticator;
use App\Core\Validator;
use App\Core\App;

class RegistrationController {
  public function create () {
    view("registration/create.view.php");
  }
  public function store () {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $errors = [];
    if(!Validator::email($email)) {
      $errors["email"] = "Please input a valid email address";
    }
    if(!Validator::string($password,7, 255)) {
      $errors["password"] = "Password must be between 7 and 255 length long";
    }

    if(!empty($errors)) {
      return view("registration/create.view.php", [
        "errors" => $errors
      ]);
    }

    $db = App::resolve("Core\Database");

    $user = $db->query("SELECT * FROM users WHERE email = :email", ["email" => $email])->fetch();

    if($user) {
      header("location: /");
      exit();
    } else {
      $db->query("INSERT INTO users(email, password) VALUE(:email, :password)", ["email" => $email, "password" => password_hash($password, PASSWORD_DEFAULT)]);
      
      Authenticator::login($user);

      header("location: /");
      exit();
    }
  }
}
  
?>