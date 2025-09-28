<?php

use Core\Database;
use Core\Response;
use Core\Router;
use Core\Validator;

$banner_title = 'Edit Note';

$config = requireFromBase('config.php');
$db = new Database($config['database'], 'root', '');

$tempUserId = 1;

// This controller only handles PATCH requests (routed by Router)

// Get note_id from URL parameters
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method']) && strtoupper($_POST['_method']) === 'PATCH') {
    $body = $_POST['body'];
    $validator = new Validator();
    
    $isValid = $validator->validate(['body' => $body], [
        'body' => ['required', 'max:1000']
    ]);
    
    $errors = $validator->errors();

    if ($isValid) {
        // Update the note
        $db->query("UPDATE notes SET body = ? WHERE note_id = ? AND user_id = ?", [
            $body, 
            $noteId, 
            $tempUserId
        ]);
        
        // Redirect to the updated note
        Router::push("/note?id={$noteId}");
        exit;
    }
} else {
    // If not a PATCH request, redirect to edit form
    Router::push("/note/edit?id={$noteId}");
    exit;
}

// If validation failed, show the edit form with errors
requireFromView("note/edit.view.php", ['banner_title' => $banner_title, 'note' => $note, 'errors' => $errors ?? []]);
