<?php

use Core\Security;

$banner_title = 'Create Note';
$csrfToken = Security::generateCsrfToken();

requireFromView('note/create.view.php', ['banner_title' => $banner_title, 'csrfToken' => $csrfToken]);
