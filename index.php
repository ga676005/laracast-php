<?php

$routes = require 'routes.php';
const BASE_PATH = __DIR__ . '/';

require BASE_PATH . 'helpers.php';
require BASE_PATH . 'Router.php';

// Create router instance
$router = new Router();

// Define routes
foreach ($routes as $route => $controller) {
    $router->get($route, $controller);
}

// Resolve the current request
$router->resolve();