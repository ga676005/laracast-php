<?php 
require 'Response.php';

$banner_title = 'Note';

$config = require 'config.php';
require 'Database.php';
$db = new Database($config['database'], 'root', '');

$note = $db->query("SELECT * FROM notes WHERE note_id = ?", [$_GET['id']])->fetch();

if (!$note) {
    $router->resolve(Response::NOT_FOUND);
}

$tempUserId = 1;
isAuthorized($note['user_id'] === $tempUserId);

require BASE_PATH . "views/note.view.php";