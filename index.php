<?php
session_start();
require_once 'config.php';
require_once 'autorequire.php';
require_once 'routes.php';

use application\Router;

//function exceptions_error_handler($severity, $message, $filename, $lineno) {
//    throw new ErrorException($message, 0, $severity, $filename, $lineno);
//}
//
//function fatal_handler() {
//    throw new ErrorException("FATAL ERROR", 500);
//}
//
//set_error_handler('exceptions_error_handler');
//register_shutdown_function( "fatal_handler" );

$router = new Router();
$router->load(routes());
echo $router->start();