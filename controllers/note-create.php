<?php 
require 'Response.php';

$banner_title = 'Create Note';

$config = require 'config.php';
require 'Database.php';
$db = new Database($config['database'], 'root', '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = $_POST['body'];
    if (!empty($body)) {
        $db->query("INSERT INTO notes (body, user_id) VALUES (:body, :user_id)", ['body' => $body, 'user_id' => 1]);
        Router::push('/notes');
        exit;
    }
}

require BASE_PATH . "views/note-create.view.php";