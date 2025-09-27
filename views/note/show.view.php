<?php 
require(BASE_PATH . 'views/partials/header.php'); 
require(BASE_PATH . 'views/partials/nav.php'); 
require(BASE_PATH . 'views/partials/banner.php'); 
?>


<main>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <a href="<?= Router::url('/notes') ?>" class="text-blue-600 hover:text-blue-800 underline transition-colors duration-200">Back to notes</a>
        <h1><?= htmlspecialchars($note['body']) ?></h1>
    </div>
</main>


<?php require(BASE_PATH . 'views/partials/footer.php'); ?>