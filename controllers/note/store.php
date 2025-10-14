<?php

use Core\App;
use Core\Database;
use Core\Router;
use Core\Validator;

$banner_title = 'Create Note';

/** @var Database $db */
$db = App::resolve(Database::class);

$body = $_POST['body'];
$validator = new Validator();

$isValid = $validator->validate(['body' => $body], [
    'body' => ['required', 'max:1000'],
]);

$errors = $validator->errors();

if ($isValid) {
    $db->query('INSERT INTO notes (body, user_id) VALUES (:body, :user_id)', [
        'body' => $body,
        'user_id' => $_SESSION['user']['user_id'],
    ]);
    $note_id = $db->lastInsertId();
    Router::push("/note?id={$note_id}");
    exit;
}

// If validation failed, set flash messages and redirect to GET create form
flash('errors', $errors);
flash('body', $body); // Preserve user input
Router::push('/notes/create');
exit;
