<?php 
$banner_title = 'Notes';

$config = requireFromBase('config.php');
$db = new Database($config['database'], 'root', '');

$notes = $db->query("SELECT * FROM notes WHERE user_id = 1")->fetchAll();

requireFromView("note/index.view.php", ['banner_title' => $banner_title, 'notes' => $notes]);