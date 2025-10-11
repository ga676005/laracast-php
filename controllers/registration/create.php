<?php

use Core\Security;

$banner_title = 'Sign up';
$csrfToken = Security::generateCsrfToken();

requireFromView('registration/create.view.php', ['banner_title' => $banner_title, 'csrfToken' => $csrfToken]);
