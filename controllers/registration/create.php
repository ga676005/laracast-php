<?php

use Core\Security;
use Core\Session;

$banner_title = 'Sign up';
$csrfToken = Security::generateCsrfToken();

// Get flash messages for display
$errors = Session::flash('errors') ?? [];
$oldEmail = Session::flash('email') ?? '';

requireFromView('registration/create.view.php', [
    'banner_title' => $banner_title, 
    'csrfToken' => $csrfToken,
    'errors' => $errors,
    'oldEmail' => $oldEmail
]);
