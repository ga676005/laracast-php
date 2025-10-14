<?php

use Core\Router;
use Core\Session;

// Log the sign out action
$user = Session::getUser();
if ($user) {
    logInfo('User signed out', ['email' => $user['email']]);
}

// Clear the session securely
Session::destroy();

// Redirect to sign in page
Router::push('/signin');
