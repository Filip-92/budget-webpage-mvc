<?php

/**
 * Front controller
 *
 * PHP version 7.0
 */

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';


/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');


/**
 * Sessions
 */
session_start();


/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
//$router->add('', ['controller' => 'Balances', 'action' => 'main']);
$router->add('balance', ['controller' => 'Balances', 'action' => 'currentMonth']);
$router->add('balance', ['controller' => 'Balances', 'action' => 'current']);
$router->add('balance_previous', ['controller' => 'Balances', 'action' => 'previousMonth']);
$router->add('balance_previous', ['controller' => 'Balances', 'action' => 'previous']);
$router->add('balance_current_year', ['controller' => 'Balances', 'action' => 'currentYear']);
$router->add('balance_current_year', ['controller' => 'Balances', 'action' => 'year']);
$router->add('balance_custom', ['controller' => 'Balances', 'action' => 'custom']);
$router->add('balance/modal', ['controller' => 'Balances', 'action' => 'modal']);
$router->add('income', ['controller' => 'Items', 'action' => 'addIncome']);
$router->add('expense', ['controller' => 'Items', 'action' => 'addExpense']);
$router->add('login', ['controller' => 'Login', 'action' => 'new']);
$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);
$router->add('settings', ['controller' => 'Settings', 'action' => 'index']);
$router->add('password/reset/{token:[\da-f]+}', ['controller' => 'Password', 'action' => 'reset']);
$router->add('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']);
$router->add('account/delete_success', ['controller' => 'Account', 'action' => 'deleteSuccess']);
$router->add('{controller}/{action}');

$router->dispatch($_SERVER['QUERY_STRING']);
