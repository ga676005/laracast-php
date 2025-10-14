<?php

use Core\App;
use Core\Database;
use Core\Response;
use Core\Security;

$banner_title = 'Edit Note';

/** @var Database $db */
$db = App::resolve(Database::class);

// Only handle GET requests for showing the edit form
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    exit;
}

$note = $db->query('SELECT * FROM notes WHERE note_id = ?', [$_GET['id']])->fetch();

if (!$note) {
    $router->resolve(Response::NOT_FOUND);
}

// Check authorization - user can only edit their own notes
authorize($note['user_id'] === $_SESSION['user']['user_id']);

$csrfToken = Security::generateCsrfToken();

// Get flash messages for display
$errors = flash('errors') ?? [];
$oldBody = flash('body') ?? '';

// Use flash data if available, otherwise use note data
$bodyValue = $oldBody ?: $note['body'];

requireFromView('note/edit.view.php', [
    'banner_title' => $banner_title, 
    'note' => $note, 
    'csrfToken' => $csrfToken,
    'errors' => $errors,
    'bodyValue' => $bodyValue
]);
