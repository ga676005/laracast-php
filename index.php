<?php


const BASE_PATH = __DIR__ . '/';

require BASE_PATH . 'helpers.php';
require BASE_PATH . 'router.php';

// Create router instance
$router = new Router();

// Define routes
$router->get('/', 'controllers/index.php');
$router->get('/home', 'controllers/index.php');
$router->get('/about', 'controllers/about.php');
$router->get('/notes', 'controllers/notes.php');
$router->get('/note', 'controllers/note.php');
$router->get('/contact', 'controllers/contact.php');

// Resolve the current request
$router->resolve();