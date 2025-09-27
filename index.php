<?php

const BASE_PATH = __DIR__ . '/';

$routes = require BASE_PATH . 'routes.php';
require BASE_PATH . 'core/helpers.php';

spl_autoload_register(function ($class) {
    require BASE_PATH . "core/{$class}.php";
});

// Create router instance
$router = new Router();

// Define routes
foreach ($routes as $route => $controller) {
    $router->get($route, $controller);
}

// Resolve the current request
$router->resolve();