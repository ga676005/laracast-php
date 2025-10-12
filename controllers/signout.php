<?php

use Core\Router;
use Core\Security;

// Log the sign out action
if (isset($_SESSION['user']['email'])) {
    logInfo('User signed out', ['email' => $_SESSION['user']['email']]);
}

// Clear the session securely
Security::destroySession();

// Redirect to sign in page
Router::push('/signin');
