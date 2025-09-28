<?php

// BASE_PATH 永遠都用入口檔，因為 index.php 放在 public，所以 BASE_PATH 要回到上一層
const BASE_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
// 這裡的路徑只能用串的，不能用 requireFromBase 因為還沒執行到 helpers.php
require BASE_PATH . 'core/helpers.php';
setupClassAutoLoader();

// 之後載入路徑都用 requireFromBase
$routes = requireFromBase('routes.php');

// Import Core classes
use Core\Router;

// Create router instance
$router = new Router();

// Define routes
foreach ($routes as $route => $controller) {
    $router->get($route, $controller);
}

// Resolve the current request
$router->resolve();