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
    requireFromView('signin.view.php', ['csrfToken' => $csrfToken]);
    exit;
}

// if request method is POST, check if email and password are correct
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!Security::validateCsrfToken($_POST['_token'] ?? '')) {
        $errors['error'] = 'Invalid request. Please try again.';
        $csrfToken = Security::generateCsrfToken();
        requireFromView('signin.view.php', ['errors' => $errors, 'csrfToken' => $csrfToken]);
        exit;
    }

    $email = Security::sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($email) || empty($password)) {
        $errors['error'] = 'Email and password are required.';
        $csrfToken = Security::generateCsrfToken();
        requireFromView('signin.view.php', ['errors' => $errors, 'csrfToken' => $csrfToken]);
        exit;
    }

    if (!Security::validateEmail($email)) {
        $errors['error'] = 'Please enter a valid email address.';
        $csrfToken = Security::generateCsrfToken();
        requireFromView('signin.view.php', ['errors' => $errors, 'csrfToken' => $csrfToken]);
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
        ];
        $_SESSION['last_activity'] = time();

        Router::push('/home');
        exit;
    }

    // Log failed login attempt
    error_log("Failed login attempt for email: {$email} from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));

    $errors['error'] = 'Invalid email or password';
    $csrfToken = Security::generateCsrfToken();
    requireFromView('signin.view.php', ['errors' => $errors, 'csrfToken' => $csrfToken]);
    exit;
}
