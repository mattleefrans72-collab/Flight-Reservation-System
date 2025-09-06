<?php

namespace App\Core;

class ValidationExeption extends \Exception{
  public readonly array $errors;    //readonly changes once
  public readonly array $old;
  public static function throw($errors, $old) {
    $instance = new ValidationExeption();

    $instance->errors = $errors;
    $instance->old = $old;
    
    throw $instance;
  }

}

?>