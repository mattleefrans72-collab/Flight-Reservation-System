<?php

$router->get(uri: '/', controller: 'App\\Controllers\\HomeController@index');
$router->get('/flight', 'App\\Controllers\\FlightController@index');

$router->get('/search', 'App\\Controllers\\SearchController@index');

?>