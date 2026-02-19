<?php

namespace Config;

$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// Public routes
$routes->get('/', 'Home::index');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::attemptRegister');
$routes->get('logout', 'Auth::logout');

// Password Reset (public)
$routes->get('password-reset/forgot', 'PasswordReset::forgot');
$routes->post('password-reset/send', 'PasswordReset::sendResetLink');
$routes->get('password-reset/reset/(:any)', 'PasswordReset::reset/$1');
$routes->post('password-reset/update', 'PasswordReset::updatePassword');

// Help & Contact (public)
$routes->get('help', 'Help::index');
$routes->get('contact', 'Contact::index');
$routes->post('contact/send', 'Contact::send');

// Protected routes (require authentication)
$routes->group('', ['filter' => 'auth'], function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');
    
    // User Profile
    $routes->get('profile', 'Profile::index');
    $routes->post('profile/update', 'Profile::update');
    
    // Start Position (Netherlands)
    $routes->get('start-position', 'StartPosition::index');
    $routes->post('start-position/save', 'StartPosition::save');
    
    // Income
    $routes->get('income', 'Income::index');
    $routes->post('income/save', 'Income::save');
    
    // Italy Property
    $routes->get('property', 'Property::index');
    $routes->post('property/save', 'Property::save');
    
    // Expenses
    $routes->get('expenses', 'Expenses::index');
    $routes->post('expenses/save', 'Expenses::save');
    
    // Taxes
    $routes->get('taxes', 'Taxes::index');
    $routes->post('taxes/save', 'Taxes::save');
    
    // B&B Module
    $routes->get('bnb', 'Bnb::index');
    $routes->post('bnb/settings/save', 'Bnb::saveSettings');
    $routes->post('bnb/expenses/save', 'Bnb::saveExpenses');
    $routes->get('bnb/breakeven', 'Bnb::breakeven');
    
    // Scenarios
    $routes->get('scenarios', 'Scenarios::index');
    $routes->post('scenarios/save', 'Scenarios::save');
    $routes->get('scenarios/load/(:num)', 'Scenarios::load/$1');
    $routes->post('scenarios/delete/(:num)', 'Scenarios::delete/$1');
    
    // Export
    $routes->get('export/csv', 'Export::csv');
    $routes->get('export/pdf', 'Export::pdf');
});

// Admin routes (require admin role)
$routes->group('admin', ['filter' => 'admin'], function ($routes) {
    $routes->get('/', 'Admin::index');
    $routes->get('users', 'Admin::users');
    $routes->get('users/create', 'Admin::createUser');
    $routes->post('users/store', 'Admin::storeUser');
    $routes->get('users/edit/(:num)', 'Admin::editUser/$1');
    $routes->post('users/update/(:num)', 'Admin::updateUser/$1');
    $routes->post('users/delete/(:num)', 'Admin::deleteUser/$1');
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
