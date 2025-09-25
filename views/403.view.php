<?php 
require('partials/header.php'); 
require('partials/nav.php'); 
?>


<main>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold">
            You are not authorized to access this page.
        </h1>
        <a href="<?= Router::url('/') ?>" class="text-blue-500">Go back home.</a>
    </div>
</main>


<?php require('partials/footer.php'); ?>