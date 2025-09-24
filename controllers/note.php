<?php 
$banner_title = 'Note';

$config = require 'config.php';
require 'Database.php';
$db = new Database($config['database'], 'root', '');

$note = $db->query("SELECT * FROM notes WHERE note_id = ?", [$_GET['id']])->fetch();

require BASE_PATH . "views/note.view.php";