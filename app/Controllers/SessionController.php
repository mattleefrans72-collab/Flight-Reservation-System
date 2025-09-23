<?php
namespace App\Controllers;

use App\Core\Authenticator;
use App\Core\Session;
use App\Http\Forms\LoginForm;

class SessionController {
  public function create () {
    view("session/create.view.php", ["errors" => Session::get("errors") ?? []]);
  }
  public function destroy () {
    Authenticator::logout();

    header("location: /");
    exit();
  }
  public function store () {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $form = LoginForm::validate(["email" => $email, "password" => $password]);

    $sighnedIn = (new Authenticator)->attemp($email, $password);

    if (!$sighnedIn) {
      $form->error("email", "No matching account fount for that email and current password")->throw();
    } 

    redirect('/');
  }
}