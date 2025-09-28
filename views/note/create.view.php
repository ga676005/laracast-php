<?php

use Core\Router;

requireFromView('partials/header.php');
requireFromView('partials/nav.php');
requireFromView('partials/banner.php', ['banner_title' => $banner_title]);
?>


<main>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <form method="post"
            action="<?= Router::url('/notes') ?>">
            <fieldset class="fieldset">
                <legend class="fieldset-legend">Body</legend>
                <textarea
                  name="body"
                  class="textarea h-24"
                  placeholder="Enter your note here~"
                ><?= isset($_POST['body']) ? htmlspecialchars($_POST['body']) : '' ?></textarea>
                <!-- ^^^ teaxarea > < 中間不能有空格，不然輸入框就會多那些空格 -->


                <?php if (isset($errors['body'])): ?>
                <p class="text-red-500">
                    <?= $errors['body'] ?>
                </p>
                <?php endif; ?>
            </fieldset>
            <button class="btn btn-soft btn-primary btn-sm mt-2">Create Note</button>
        </form>
    </div>
</main>


<?php requireFromView('partials/footer.php'); ?>