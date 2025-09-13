<?php
namespace App\Http\Forms;

use App\Core\ValidationExeption;
use App\Core\Validator;
class FlightForm {
    protected $errors = [];
    protected array $attribute;
  public function __construct(array $attribute) {
    $this->attribute = $attribute;

    if(!Validator::airport($attribute["originLocationCode"])) {
      $this->errors["from"] = "Please input a valid airport code";
    }
    if(!Validator::airport($attribute["destinationLocationCode"])) {
      $this->errors["to"] = "Please input a valid airport code";
    }
    if(($attribute["departureDate"]) >= ($attribute["returnDate"])) {
      $this->errors["dates"] = "Please input valid dates";
    }
    if(!(Validator::positive($attribute["adults"]) && !Validator::positive($attribute["children"]))) {
      $this->errors["passenger"] = "Please input a valid number of passenger";
    }
  }
  public static function validate($attribute) {
    $instance = new FlightForm($attribute);

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