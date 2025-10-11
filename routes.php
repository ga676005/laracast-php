<?php

// Define all routes using router methods

// Public routes (no middleware)
$router->get('/about', 'controllers/about.php');
$router->get('/contact', 'controllers/contact.php');

// Guest-only routes (redirect to /home if logged in)
$router->middleware('guest')->get('/signin', 'controllers/signin.php');
$router->middleware(['guest', 'csrf'])->post('/signin', 'controllers/signin.php');
$router->middleware('guest')->get('/signup', 'controllers/registration/create.php');
$router->middleware(['guest', 'csrf'])->post('/signup', 'controllers/registration/store.php');

// Web routes (session-based authentication)
$router->middleware('session-auth')->get('/', 'controllers/index.php');
$router->middleware('session-auth')->get('/home', 'controllers/index.php');
$router->middleware('session-auth')->get('/notes', 'controllers/note/index.php');
$router->middleware('session-auth')->get('/note', 'controllers/note/show.php');
$router->middleware('session-auth')->get('/note/edit', 'controllers/note/edit.php');
$router->middleware('session-auth')->get('/notes/create', 'controllers/note/create.php');
$router->middleware(['session-auth', 'csrf'])->post('/notes', 'controllers/note/store.php');
$router->middleware(['session-auth', 'csrf'])->post('/note', 'controllers/note/update.php');
$router->middleware(['session-auth', 'csrf'])->delete('/note', 'controllers/note/delete.php');
$router->middleware(['session-auth', 'csrf'])->patch('/note', 'controllers/note/update.php');

// Sign out (no middleware - accessible to both logged in and guest users)
$router->get('/signout', 'controllers/signout.php');
$router->post('/signout', 'controllers/signout.php');

// API routes (token-based authentication - NO CSRF, NO SESSIONS)
$router->middleware('api-auth')->get('/api/notes', 'controllers/api/note/store.php');
$router->middleware('api-auth')->post('/api/notes', 'controllers/api/note/store.php');
