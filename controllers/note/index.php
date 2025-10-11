<?php

use Core\App;
use Core\Database;

$banner_title = 'Notes';

/** @var Database $db */
// ::class returns fully qualified class name as string
// ::class gets "Core\Database" string for container lookup
$db = App::resolve(Database::class);

$notes = $db->query('SELECT * FROM notes WHERE user_id = ?', [$_SESSION['user']['user_id']])->fetchAll();

requireFromView('note/index.view.php', ['banner_title' => $banner_title, 'notes' => $notes]);
