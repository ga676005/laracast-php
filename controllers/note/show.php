<?php

use Core\App;
use Core\Database;
use Core\Response;
use Core\Security;

$banner_title = 'Note';

/** @var Database $db */
$db = App::resolve(Database::class);

// Only handle GET requests for showing the note
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    exit;
}

$note = $db->query('SELECT * FROM notes WHERE note_id = ?', [$_GET['id']])->fetch();

if (!$note) {
    $router->resolve(Response::NOT_FOUND);
}

// Check authorization - user can only view their own notes
authorize($note['user_id'] === $_SESSION['user']['user_id']);

$csrfToken = Security::generateCsrfToken();

requireFromView('note/show.view.php', ['banner_title' => $banner_title, 'note' => $note, 'csrfToken' => $csrfToken]);
