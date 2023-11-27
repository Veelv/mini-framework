<?php

use Config\Router\Route;

$router = new Route();

// $router->method('GET', '', 'HomeController@index');
$router->get('', 'HomeController@index');