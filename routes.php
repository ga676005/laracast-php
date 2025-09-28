<?php

// Define all routes using router methods
$router->get('/', 'controllers/index.php');
$router->get('/home', 'controllers/index.php');
$router->get('/about', 'controllers/about.php');
$router->get('/contact', 'controllers/contact.php');


$router->get('/notes', 'controllers/note/index.php');
$router->get('/note', 'controllers/note/show.php');
$router->get('/note/edit', 'controllers/note/edit.php');
$router->get('/notes/create', 'controllers/note/create.php');
$router->post('/notes', 'controllers/note/create.php');
$router->post('/note', 'controllers/note/update.php');

$router->delete('/note', 'controllers/note/delete.php');

$router->patch('/note', 'controllers/note/update.php');

