<?php

use Core\Log;
use Core\Router;
use Core\Session;

// Log the sign out action
$user = Session::getUser();
if ($user) {
    Log::info('User signed out', ['email' => $user['email']]);
}

// Clear the session securely
Session::destroy();

// Redirect to sign in page
Router::push('/signin');
