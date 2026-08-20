<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/penyedia', 'PenyediaController::index');
$routes->get('/proses-penyedia/(:num)', 'PenyediaController::proses_penyedia/$1');
$routes->get('/proses-kbli/(:num)', 'PenyediaController::proses_kbli/$1');
$routes->post('/proses-kbli/(:num)', 'PenyediaController::proses_kbli/$1');
