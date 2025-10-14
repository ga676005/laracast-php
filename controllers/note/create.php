<?php

use Core\Security;

$banner_title = 'Create Note';
$csrfToken = Security::generateCsrfToken();

// Get flash messages for display
$errors = flash('errors') ?? [];
$oldBody = flash('body') ?? '';

requireFromView('note/create.view.php', [
    'banner_title' => $banner_title, 
    'csrfToken' => $csrfToken,
    'errors' => $errors,
    'oldBody' => $oldBody
]);
