<?php 
namespace App\Core;
class Validator {
  public static function string ($value, $min = 1, $max = 10) {
    $string = trim($value);
    return strlen($string) >= $min && strlen($string) <= $max;
  } 
  public static function email($value) {
    return filter_var($value, FILTER_VALIDATE_EMAIL);
  }
  public static function positive($value) {
    return $value >= 1;
  }
  public static function airport($value) {
    $string = trim($value);
    return strlen($string) == 3;
  }
}
?>