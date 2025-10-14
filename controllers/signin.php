<?php

use Core\App;
use Core\Database;
use Core\Router;
use Core\Security;

/** @var Database $db */
$db = App::resolve(Database::class);

// if request method is GET, show signin form
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $csrfToken = Security::generateCsrfToken();
    
    // Get flash messages for display
    $errors = [];
    if (flashExists('error')) {
        $errors['error'] = flash('error');
    }
    if (flashExists('email')) {
        $oldEmail = flash('email');
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
        flash('error', 'Invalid request. Please try again.');
        Router::push('/signin');
        exit;
    }

    $email = Security::sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($email) || empty($password)) {
        flash('error', 'Email and password are required.');
        flash('email', $email); // Preserve email for user convenience
        Router::push('/signin');
        exit;
    }

    if (!Security::validateEmail($email)) {
        flash('error', 'Please enter a valid email address.');
        flash('email', $email); // Preserve email for user convenience
        Router::push('/signin');
        exit;
    }

    // check if email and password are correct
    $user = $db->query('SELECT * FROM users WHERE email = :email', ['email' => $email])->fetch();
    if ($user && Security::verifyPassword($password, $user['password'])) {
        // Regenerate session ID for security
        Security::regenerateSessionId();

        $_SESSION['user'] = [
            'email' => $user['email'],
            'user_id' => $user['user_id'],
            'role' => $user['role'] ?? 'user', // Default to 'user' role if not set
        ];
        $_SESSION['last_activity'] = time();

        Router::push('/home');
        exit;
    }

    // Log failed login attempt
    logWarning('Failed login attempt', [
        'email' => $email,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    ]);

    flash('error', 'Invalid email or password');
    flash('email', $email); // Preserve email for user convenience
    Router::push('/signin');
    exit;
}
