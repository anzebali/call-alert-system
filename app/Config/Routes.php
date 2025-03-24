<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('auth/login', 'Auth::login');
$routes->get('auth/callback', 'Auth::callback');
$routes->post('auth/update-phone', 'Auth::updatePhoneNumber');
$routes->get('auth/logout', 'Auth::logout');
$routes->get('dashboard', 'DashboardController::index');
$routes->get('twilio/call', 'TwilioController::sendReminder');


