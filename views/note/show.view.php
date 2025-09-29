<?php

use Core\Router;

requireFromView('partials/header.php');
requireFromView('partials/nav.php');
requireFromView('partials/banner.php', ['banner_title' => $banner_title]);
?>


<main>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <a href="<?= Router::url('/notes') ?>"
            class="text-blue-600 hover:text-blue-800 underline transition-colors duration-200">
            Back to notes
        </a>
        <h1><?= htmlspecialchars($note['body']) ?>
        </h1>
        <div class="mt-4 flex gap-2">
            <a href="<?= Router::url('/note/edit?id=' . $note['note_id']) ?>"
                class="btn btn-soft btn-primary btn-sm">
                Edit Note
            </a>
            <form method="post"
                action="<?= Router::url('/note?id=' . $note['note_id']) ?>"
                onsubmit="return confirm('Are you sure you want to delete this note?')">
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="btn btn-soft btn-error btn-sm">Delete Note</button>
            </form>
        </div>
    </div>
</main>


<?php requireFromView('partials/footer.php'); ?>