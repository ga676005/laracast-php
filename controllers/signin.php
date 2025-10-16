<?php

use Core\App;
use Core\Database;
use Core\Router;
use Core\Security;
use Core\Session;

/** @var Database $db */
$db = App::resolve(Database::class);

// if request method is GET, show signin form
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $csrfToken = Security::generateCsrfToken();
    
    // Get flash messages for display
    $errors = [];
    if (Session::flashExists('error')) {
        $errors['error'] = Session::flash('error');
    }
    if (Session::flashExists('email')) {
        $oldEmail = Session::flash('email');
    }
    
    requireFromView('signin.view.php', [
        'csrfToken' => $csrfToken,
        'errors' => $errors,
        'oldEmail' => $oldEmail ?? ''
    ]);
    exit;
}

// if request method is POST, check if email and password are correct
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!Security::validateCsrfToken($_POST['_token'] ?? '')) {
        Session::flash('error', 'Invalid request. Please try again.');
        Router::push('/signin');
        exit;
    }

    $email = Security::sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($email) || empty($password)) {
        Session::flash('error', 'Email and password are required.');
        Session::flash('email', $email); // Preserve email for user convenience
        Router::push('/signin');
        exit;
    }

    if (!Security::validateEmail($email)) {
        Session::flash('error', 'Please enter a valid email address.');
        Session::flash('email', $email); // Preserve email for user convenience
        Router::push('/signin');
        exit;
    }

    // check if email and password are correct
    $user = $db->query('SELECT * FROM users WHERE email = :email', ['email' => $email])->fetch();
    if ($user && Security::verifyPassword($password, $user['password'])) {
        // Regenerate session ID for security
        Session::regenerateId();

        Session::setUser([
            'email' => $user['email'],
            'user_id' => $user['user_id'],
            'role' => $user['role'] ?? 'user', // Default to 'user' role if not set
        ]);

        // Redirect to previous URL or default to home
        $redirectUrl = $_GET['previousurl'] ?? '/home';
        
        Router::push($redirectUrl);
        exit;
    }

    // Log failed login attempt
    logWarning('Failed login attempt', [
        'email' => $email,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    ]);

    Session::flash('error', 'Invalid email or password');
    Session::flash('email', $email); // Preserve email for user convenience
    Router::push('/signin');
    exit;
}
