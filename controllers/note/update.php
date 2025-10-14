<?php

use Core\App;
use Core\Database;
use Core\Response;
use Core\Router;
use Core\Session;
use Core\Validator;

$banner_title = 'Edit Note';

/** @var Database $db */
$db = App::resolve(Database::class);

// This controller only handles PATCH requests (routed by Router)

// Get note_id from URL parameters
$noteId = $_GET['id'] ?? null;

if (!$noteId) {
    http_response_code(400); // Bad Request
    exit;
}

// First check if the note exists and belongs to the user
$note = $db->query('SELECT * FROM notes WHERE note_id = ?', [$noteId])->fetch();

if (!$note) {
    $router->resolve(Response::NOT_FOUND);
}

// Check authorization - user can only update their own notes
$user = Session::getUser();
authorize($note['user_id'] === $user['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method']) && strtoupper($_POST['_method']) === 'PATCH') {
    $body = $_POST['body'];
    $validator = new Validator();

    $isValid = $validator->validate(['body' => $body], [
        'body' => ['required', 'max:1000'],
    ]);

    $errors = $validator->errors();

    // If validation failed, set flash messages and redirect to GET edit form
    if (!$isValid) {
        Session::flash('errors', $errors);
        Session::flash('body', $body); // Preserve user input
        Router::push("/note/edit?id={$noteId}");
        exit;
    }

    // Update the note
    $db->query('UPDATE notes SET body = ? WHERE note_id = ? AND user_id = ?', [
        $body,
        $noteId,
        $user['user_id'],
    ]);

    // Redirect to the updated note
    Router::push("/note?id={$noteId}");
    exit;
} else {
    // If not a PATCH request, redirect to edit form
    Router::push("/note/edit?id={$noteId}");
    exit;
}
