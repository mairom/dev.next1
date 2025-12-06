<?php

use CodeIgniter\Router\RouteCollection;
$routes->setAutoRoute(false);
/**
 * @var RouteCollection $routes
 */

 
$routes->get('/', 'Home::index');

$routes->post('(:segment)', 'DynamicController::execute/$1');
$routes->get('(:segment)', 'DynamicController::execute/$1');

$routes->post('(:segment)/(:segment)', 'DynamicController::execute/$1/$2');
$routes->get('(:segment)/(:segment)', 'DynamicController::execute/$1/$2');


$routes->post('(:segment)/(:segment)/(:segment)', 'DynamicController::execute/$1/$2/$3');
$routes->get('(:segment)/(:segment)/(:segment)', 'DynamicController::execute/$1/$2/$3');


$routes->post('(:segment)/(:segment)/(:segment)/(:segment)', 'DynamicController::execute/$1/$2/$3/$4');
$routes->get('(:segment)/(:segment)/(:segment)/(:segment)', 'DynamicController::execute/$1/$2/$3/$4');