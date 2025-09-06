<?php 

namespace App\Core;
class App {
  protected static $contanier;
  public static function setContanier($contanier) {
    App::$contanier = $contanier;
  }
  public static function contanier() {
    return App::$contanier;
  }
  public static function bind($key, $resolve) {
    App::contanier()->bind($key, $resolve);
  }
  public static function resolve($key) {
    return App::contanier()->resolve($key);
  }
}
?>