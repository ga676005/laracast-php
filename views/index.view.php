<?php
require BASE_PATH . 'views/partials/header.php';
require BASE_PATH . 'views/partials/nav.php';
require BASE_PATH . 'views/partials/banner.php';
?>


<main>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
       Hello, <?= $_SESSION['user']['email'] ?? 'Guest' ?>! Welcome to the home page!
    </div>
</main>


<?php require BASE_PATH . 'views/partials/footer.php'; ?>