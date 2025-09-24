<?php 
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
                        <?= $note['body'] ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</main>


<?php require(BASE_PATH . 'views/partials/footer.php'); ?>