<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// API Routes
$routes->group('api', static function ($routes) {
    $routes->get('dashboard', 'Api::dashboard');
    $routes->get('health', 'Api::health');
    
    // Add more API endpoints here
});

// Catch-all route for React SPA
$routes->get('/', 'Home::index');

$routes->group('api', function($routes) {
    $routes->get('test-db', 'TestDB::index');
    
    // Auth routes
    $routes->post('auth/login', 'API\Auth::login');
    $routes->get('auth/me', 'API\Auth::me');
    $routes->post('auth/logout', 'API\Auth::logout');
    // Modul 1-8 Placeholders
    $routes->group('paket', function($routes) {});
    $routes->group('permohonan', function($routes) {
        $routes->get('/', 'API\Permohonan::index');
    });
    $routes->group('rekanan', function($routes) {
        $routes->get('/', 'API\Rekanan::index');
    });
    $routes->group('bidding', function($routes) {});
    $routes->group('evaluasi', function($routes) {});
    $routes->group('contracting', function($routes) {});
    $routes->group('purchasing', function($routes) {});
    $routes->group('master', function($routes) {});
});

$routes->get('(:any)', 'Home::index');
