<?php

namespace App\Http\Forms;

use App\Core\ValidationExeption;
use App\Core\Validator;
class LoginForm {
  protected $errors = [];
  public function __construct(public array $attribute) {
    if(!Validator::email($attribute["email"])) {
      $this->errors["email"] = "Please input a valid email address";
    }
    if(!Validator::string($attribute["password"])) {
      $this->errors["password"] = "Invalid password";
    }
  }
  public static function validate($attribute) {
    $instance = new LoginForm($attribute);

    return $instance->failed() ? $instance->throw() : $instance;
  }
  public function throw() {
    ValidationExeption::throw($this->errors(), $this->attribute);
  }
  public function failed() {
    return count($this->errors);
  }
  public function errors() {
    return $this->errors;
  }
  public function error($field, $message) {
    $this->errors[$field] = $message;
    return $this;
  }
}


?>