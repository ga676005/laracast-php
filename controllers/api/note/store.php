<?php

use Core\App;
use Core\Database;
use Core\Security;

/** @var Database $db */
$db = App::resolve(Database::class);

// Set CORS headers for API
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Handle different HTTP methods
$method = strtoupper($_SERVER['REQUEST_METHOD']);

switch ($method) {
    case 'POST':
        createNote($db);
        break;
    case 'GET':
        // Return API documentation
        showApiDocs();
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}

function createNote($db)
{
    // Get JSON input (API uses JSON, not form data)
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON input']);

        return;
    }

    $body = Security::sanitizeInput($input['body'] ?? '');

    if (empty($body)) {
        http_response_code(400);
        echo json_encode(['error' => 'Body is required']);

        return;
    }

    // Create note
    $db->query('INSERT INTO notes (body, user_id) VALUES (:body, :user_id)', [
        'body' => $body,
        'user_id' => $_SESSION['user']['user_id'],
    ]);

    $noteId = $db->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Note created successfully',
        'data' => [
            'id' => $noteId,
            'body' => $body,
            'user_id' => $_SESSION['user']['user_id'],
        ],
    ]);
}

function showApiDocs()
{
    echo json_encode([
        'api' => 'Notes API',
        'version' => '1.0',
        'endpoints' => [
            'create_note' => [
                'method' => 'POST',
                'url' => '/api/notes',
                'headers' => [
                    'X-API-Key' => 'your-api-key-here',
                    'Content-Type' => 'application/json',
                ],
                'body' => [
                    'body' => 'Note content here',
                ],
                'response' => [
                    'success' => true,
                    'message' => 'Note created successfully',
                    'data' => [
                        'id' => 123,
                        'body' => 'Note content here',
                        'user_id' => $_SESSION['user']['user_id'] ?? 'current_user',
                    ],
                ],
            ],
        ],
    ]);
}
