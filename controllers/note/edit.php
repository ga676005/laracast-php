<?php

use Core\App;
use Core\Database;
use Core\Response;

$banner_title = 'Edit Note';

/** @var Database $db */
$db = App::resolve(Database::class);

$tempUserId = 1;

// Only handle GET requests for showing the edit form
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    exit;
}

$note = $db->query('SELECT * FROM notes WHERE note_id = ?', [$_GET['id']])->fetch();

if (!$note) {
    $router->resolve(Response::NOT_FOUND);
}

authorize($note['user_id'] === $tempUserId);

requireFromView('note/edit.view.php', ['banner_title' => $banner_title, 'note' => $note]);
