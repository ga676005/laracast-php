<?php

use Core\App;
use Core\Database;
use Core\Router;
use Core\Security;
use Core\Session;
use Core\Validator;

/** @var Database $db */
$db = App::resolve(Database::class);

// Validate CSRF token
if (!Security::validateCsrfToken($_POST['_token'] ?? '')) {
    Session::flash('error', 'Invalid request. Please try again.');
    Router::push('/signup');
    exit;
}

$email = Security::sanitizeInput($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$validator = new Validator();

$isValid = $validator->validate(['email' => $email, 'password' => $password], [
    'email' => ['required', 'email'],
    'password' => ['required', 'min:3', 'max:255'],
]);

$errors = $validator->errors();

if ($isValid) {
    // check if email already exists
    $user = $db->query('SELECT * FROM users WHERE email = :email', ['email' => $email])->fetch();
    if ($user) {
        Session::flash('errors', array_merge($errors, ['email' => 'Email already exists']));
        Session::flash('email', $email); // Preserve email for user convenience
        Router::push('/signup');
        exit;
    }

    // hash password securely
    $password = Security::hashPassword($password);

    // Extract name from email (part before @)
    $name = explode('@', $email)[0];
    
    // create user with default 'user' role
    $db->query('INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)', [
        'name' => $name,
        'email' => $email,
        'password' => $password,
        'role' => 'user',
    ]);

    // Log successful registration
    logInfo('New user registered', ['email' => $email]);

    Router::push('/signin');
    exit;
} else {
    Session::flash('errors', $errors);
    Session::flash('email', $email); // Preserve email for user convenience
    Router::push('/signup');
    exit;
}
