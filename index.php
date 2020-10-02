<?php
session_start();
require_once 'config.php';
require_once 'autorequire.php';
require_once 'routes.php';

use application\Router;

$router = new Router();
$router->load(routes());
echo $router->start();