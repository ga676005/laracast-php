<?php

use Core\Router;

requireFromView('partials/header.php');
requireFromView('partials/nav.php');
requireFromView('partials/banner.php', ['banner_title' => $banner_title]);
?>


<main>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <ul>
            <?php foreach ($notes as $note): ?>
            <li>
                <a href="<?= Router::url('/note?id='.$note['note_id']) ?>"
                    class="text-blue-600 hover:text-blue-800 underline transition-colors duration-200">
                    <?= htmlspecialchars($note['body']) ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>

        <p class="mt-4">
            <a href="<?= Router::url('/notes/create') ?>"
                class="btn btn-soft btn-primary btn-sm">Create Note</a>
        </p>
    </div>
</main>


<?php requireFromView('partials/footer.php'); ?>