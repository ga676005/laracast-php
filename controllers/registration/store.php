<?php

use Core\App;
use Core\Database;
use Core\Router;
use Core\Security;
use Core\Validator;

/** @var Database $db */
$db = App::resolve(Database::class);

// Validate CSRF token
if (!Security::validateCsrfToken($_POST['_token'] ?? '')) {
    $errors['error'] = 'Invalid request. Please try again.';
    $csrfToken = Security::generateCsrfToken();
    requireFromView('registration/create.view.php', ['errors' => $errors, 'csrfToken' => $csrfToken]);
    exit;
}

$email = Security::sanitizeInput($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$validator = new Validator();

$isValid = $validator->validate(['email' => $email, 'password' => $password], [
    'email' => ['required', 'email'],
    'password' => ['required', 'min:8', 'max:255'],
]);

$errors = $validator->errors();

if ($isValid) {
    // check if email already exists
    $user = $db->query('SELECT * FROM users WHERE email = :email', ['email' => $email])->fetch();
    if ($user) {
        $errors['email'] = 'Email already exists';
        $csrfToken = Security::generateCsrfToken();
        requireFromView('registration/create.view.php', ['errors' => $errors, 'csrfToken' => $csrfToken]);
        exit;
    }

    // hash password securely
    $password = Security::hashPassword($password);

    // create user
    $db->query('INSERT INTO users (email, password) VALUES (:email, :password)', ['email' => $email, 'password' => $password]);

    // Log successful registration
    error_log("New user registered: {$email}");

    Router::push('/signin');
    exit;
} else {
    $csrfToken = Security::generateCsrfToken();
    requireFromView('registration/create.view.php', ['errors' => $errors, 'csrfToken' => $csrfToken]);
}
