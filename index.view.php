<html>

<head>
    <meta charset="UTF-8">
    <title>My Website</title>
</head>

<body>
    <?php if (empty($filteredBooks)): ?>
        <p><?= $noBooksMessage ?></p>
    <?php else: ?>
        <ul>
            <?php foreach ($filteredBooks as $book) : ?>
                <li>
                    <a href="<?= $book['purchaseUrl'] ?>">
                        <?= $book['name'] ?> (<?= $book['releaseYear'] ?>) - By <?= $book['author'] ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>

</html>