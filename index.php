<?php

require 'helpers.php';
require 'router.php';
require 'Database.php';
$config = require 'config.php';

// Create router instance
$router = new Router();

// Define routes
$router->get('/', 'controllers/index.php');
$router->get('/home', 'controllers/index.php');
$router->get('/about', 'controllers/about.php');
$router->get('/contact', 'controllers/contact.php');

// Resolve the current request
// $router->resolve();   

$db = new Database($config['database'], 'root', '');

$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id && is_numeric($id)) {
    $posts = $db->query("SELECT * FROM posts WHERE id = ?", [$id])->fetchAll();
} else {
    $posts = $db->query("SELECT * FROM posts")->fetchAll();
}

dd($posts);