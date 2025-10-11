<?php

use Core\App;
use Core\Database;
use Core\Response;
use Core\Router;

$db = App::resolve(Database::class);

// This controller only handles DELETE requests (routed by Router)

// Get note_id from URL parameters or request body
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

// Check authorization - user can only delete their own notes
authorize($note['user_id'] === $_SESSION['user']['user_id']);

// Delete the note
$db->query('DELETE FROM notes WHERE note_id = ? AND user_id = ?', [$noteId, $_SESSION['user']['user_id']]);

// Redirect to notes list
Router::push('/notes');
exit;
