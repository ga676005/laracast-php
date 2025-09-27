<?php 
require(BASE_PATH . 'views/partials/header.php'); 
require(BASE_PATH . 'views/partials/nav.php'); 
require(BASE_PATH . 'views/partials/banner.php'); 
?>


<main>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <form method="post">
            <fieldset class="fieldset">
                <legend class="fieldset-legend">Body</legend>
                <textarea name="body" class="textarea h-24" placeholder="Enter your note here~"><?= isset($_POST['body']) ? htmlspecialchars($_POST['body']) : '' ?></textarea>
                <?php if (isset($errors['body'])) : ?>
                    <p class="text-red-500"><?= $errors['body'] ?></p>
                <?php endif; ?>
            </fieldset>
            <button class="btn btn-soft btn-primary btn-sm mt-2">Create Note</button>
        </form>
    </div>
</main>


<?php require(BASE_PATH . 'views/partials/footer.php'); ?>