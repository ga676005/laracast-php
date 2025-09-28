<?php 

use Core\Router;
require(BASE_PATH . 'views/partials/header.php'); 
require(BASE_PATH . 'views/partials/nav.php'); 
require(BASE_PATH . 'views/partials/banner.php'); 
?>


<main>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <ul>
            <?php foreach ($notes as $note) : ?>
                <li>
                    <a 
                        href="<?= Router::url('/note?id=' . $note['note_id']) ?>" 
                        class="text-blue-600 hover:text-blue-800 underline transition-colors duration-200"
                    >
                        <?= htmlspecialchars($note['body']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <p class="mt-4">
            <a href="<?= Router::url('/notes/create') ?>" class="btn btn-soft btn-primary btn-sm">Create Note</a>
        </p>
    </div>
</main>


<?php require(BASE_PATH . 'views/partials/footer.php'); ?>