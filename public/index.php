<?php

// BASE_PATH 永遠都用入口檔
const BASE_PATH = __DIR__ . '/../';
// 這裡的路徑只能用串的，不能用 requireFromBase 因為還沒執行到 helpers.php
require BASE_PATH . 'core/helpers.php';
// 之後載入路徑都用 requireFromBase
$routes = requireFromBase('routes.php');

// php 內建的自動載入，如果寫 new Database()，但沒有 require 'core/Database.php'
// 這個 function 就會跑，$class 就會是使用到的 class 的名字例如 Database
// 所以我們就能把它串起來載入那個檔案
spl_autoload_register(function ($class) {
    // dd($class); // 看會是什麼東西
    requireFromBase("core/{$class}.php");
});

// Create router instance
$router = new Router();

// Define routes
foreach ($routes as $route => $controller) {
    $router->get($route, $controller);
}

// Resolve the current request
$router->resolve();