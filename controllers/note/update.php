<?php

use Core\App;
use Core\Database;
use Core\Response;
use Core\Router;
use Core\Validator;

$banner_title = 'Edit Note';

/** @var Database $db */
$db = App::resolve(Database::class);

$tempUserId = 1;

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

// Check authorization
authorize($note['user_id'] === $tempUserId);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method']) && strtoupper($_POST['_method']) === 'PATCH') {
    $body = $_POST['body'];
    $validator = new Validator();

    $isValid = $validator->validate(['body' => $body], [
        'body' => ['required', 'max:1000'],
    ]);

    $errors = $validator->errors();

    // If validation failed, show the edit form with errors
    if (!$isValid) {
        // 這裡雖然傳 'note' => $note，但因為我們在 edit.view.php 中優先抓 $note['body']
        // 所以編輯錯誤的時候才不會跳回去 note 原本的 body，而是跟 post 一樣的 body
        requireFromView('note/edit.view.php', ['banner_title' => $banner_title, 'note' => $note, 'errors' => $errors ?? []]);
        exit;
    }

    // Update the note
    $db->query('UPDATE notes SET body = ? WHERE note_id = ? AND user_id = ?', [
        $body,
        $noteId,
        $tempUserId,
    ]);

    // Redirect to the updated note
    Router::push("/note?id={$noteId}");
    exit;
} else {
    // If not a PATCH request, redirect to edit form
    Router::push("/note/edit?id={$noteId}");
    exit;
}
