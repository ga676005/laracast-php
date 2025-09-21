<?php

require 'helpers.php';
require 'router.php';

// Create router instance
$router = new Router();

// Define routes
$router->get('/', 'controllers/index.php');
$router->get('/home', 'controllers/index.php');
$router->get('/about', 'controllers/about.php');
$router->get('/contact', 'controllers/contact.php');

// Resolve the current request
$router->resolve();   