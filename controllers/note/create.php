<?php 
require 'Response.php';
require 'Validator.php';

$banner_title = 'Create Note';

$config = require 'config.php';
require 'Database.php';
$db = new Database($config['database'], 'root', '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = $_POST['body'];
    $validator = new Validator();
    
    $isValid = $validator->validate(['body' => $body], [
        'body' => ['required', 'max:1000']
    ]);
    
    $errors = $validator->errors();

    if ($isValid) {
        $db->query("INSERT INTO notes (body, user_id) VALUES (:body, :user_id)", ['body' => $body, 'user_id' => 1]);
        $note_id = $db->lastInsertId();
        Router::push("/note?id={$note_id}");
        exit;
    }
}

require BASE_PATH . "views/note/create.view.php";