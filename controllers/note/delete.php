<?php

use Core\Database;
use Core\Response;
use Core\Router;

$config = requireFromBase('config.php');
$db = new Database($config['database'], 'root', '');

$tempUserId = 1;

// This controller only handles DELETE requests (routed by Router)

// Get note_id from URL parameters or request body
$noteId = $_GET['id'] ?? null;

if (!$noteId) {
    http_response_code(400); // Bad Request
    exit;
}

// First check if the note exists and belongs to the user
$note = $db->query("SELECT * FROM notes WHERE note_id = ?", [$noteId])->fetch();

if (!$note) {
    $router->resolve(Response::NOT_FOUND);
}

// Check authorization
authorize($note['user_id'] === $tempUserId);

// Delete the note
$db->query("DELETE FROM notes WHERE note_id = ? AND user_id = ?", [$noteId, $tempUserId]);

// Redirect to notes list
Router::push("/notes");
exit;
