<?php

$router->get('/', 'App\\Controllers\\HomeController@index');
$router->get('/flight', 'App\\Controllers\\FlightController@index');

$router->get('/search', 'App\\Controllers\\SearchController@index');

$router->get('/t', 'App\\Controllers\\Test@index');

$router->get('/register', 'App\\Controllers\\RegistrationController@create')->only("guest");
$router->post('/register', 'App\\Controllers\\RegistrationController@store');

$router->get('/login', 'App\\Controllers\\SessionController@create')->only("guest");
$router->post('/session', 'App\\Controllers\\SessionController@store');
$router->delete('/session', 'App\\Controllers\\SessionController@destroy')->only("auth");
?>