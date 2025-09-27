<?php 
$banner_title = 'Notes';

$config = require 'config.php';
$db = new Database($config['database'], 'root', '');

$notes = $db->query("SELECT * FROM notes WHERE user_id = 1")->fetchAll();

require BASE_PATH . "views/note/index.view.php";