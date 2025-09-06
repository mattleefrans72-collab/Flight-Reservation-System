<?php

use App\Core\Session;
use App\Core\ValidationExeption;
use App\Core\Router;

session_start();

const BASE_PATH = __DIR__ . "/../";

require BASE_PATH . "app/Core/functions.php";


require BASE_PATH . "vendor/autoload.php";

require base_path("bootstrap.php");

$router = new Router();

require base_path ("routes.php");

$url = parse_url($_SERVER["REQUEST_URI"])["path"];

$method = $_POST["_method"] ?? $_SERVER["REQUEST_METHOD"];

try {
  $router->route($url, $method);
} catch(ValidationExeption $exception) {
  Session::flash("errors", $exception->errors);
  Session::flash("old", $exception->old);

  return redirect($router->previousURI());
}


Session::unflash();
