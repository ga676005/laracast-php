<?php

return [
    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'dbname' => 'laracast-php',
        'charset' => 'utf8mb4',
    ],
    'logging' => [
        'error_log_path' => null, // null = use storage/logs/php_errors.log, or specify custom path
        'max_log_lines' => 50,    // Number of log lines to display in the log viewer
    ],
];
