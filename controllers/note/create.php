<?php

use Core\Security;
use Core\Session;

$banner_title = 'Create Note';
$csrfToken = Security::generateCsrfToken();

// Get flash messages for display
$errors = Session::flash('errors') ?? [];
$oldBody = Session::flash('body') ?? '';

requireFromView('note/create.view.php', [
    'banner_title' => $banner_title, 
    'csrfToken' => $csrfToken,
    'errors' => $errors,
    'oldBody' => $oldBody
]);
