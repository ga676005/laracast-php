<?php

use Core\Security;

$banner_title = 'Sign up';
$csrfToken = Security::generateCsrfToken();

// Get flash messages for display
$errors = flash('errors') ?? [];
$oldEmail = flash('email') ?? '';

requireFromView('registration/create.view.php', [
    'banner_title' => $banner_title, 
    'csrfToken' => $csrfToken,
    'errors' => $errors,
    'oldEmail' => $oldEmail
]);
