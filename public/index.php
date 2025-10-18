<?php

// BASE_PATH 永遠都用入口檔，因為 index.php 放在 public，所以 BASE_PATH 要回到上一層
const BASE_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
// Load Composer autoloader，使用 composer.json 裡的 autoload
require BASE_PATH . 'vendor/autoload.php';

// Now we can use Core classes
use Core\Log;
use Core\Session;

// Configure error logging to use our custom log file
Log::configureErrorLogging();

// Start secure session
Session::start();

requireFromBase('bootstrap.php');

// Import Core classes
use Core\Router;

// Create router instance
$router = new Router();

// Define routes using router methods
requireFromBase('routes.php', ['router' => $router]);

// Resolve the current request
$router->resolve();
