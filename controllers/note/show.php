<?php 
$banner_title = 'Note';

$config = requireFromBase('config.php');
$db = new Database($config['database'], 'root', '');

$note = $db->query("SELECT * FROM notes WHERE note_id = ?", [$_GET['id']])->fetch();

if (!$note) {
    $router->resolve(Response::NOT_FOUND);
}

$tempUserId = 1;
isAuthorized($note['user_id'] === $tempUserId);

requireFromView("note/show.view.php", ['banner_title' => $banner_title, 'note' => $note]);