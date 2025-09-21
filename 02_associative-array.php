<html>

<head>
    <meta charset="UTF-8">
    <title>My Website</title>
</head>

<body>
    <?php 
        //  php 的 object 是用 []， 例如  $person = ['name' => 'dodo'];
        $books = [
            [
                'name' => 'Do Androids Dream of Electric Sheep',
                'author' => 'Philip K. Dick',
                'purchaseUrl' => 'https://www.amazon.com/Do-Androids-Dream-Electric-Sheep/dp/0451524934'
            ],
            [
                'name' => 'Project Hail Mary',
                'author' => 'Andy Weir',
                'purchaseUrl' => 'https://www.amazon.com/Project-Hail-Mary-Andy-Weir/dp/1524759299'
            ],
            [
                'name' => 'The Martian',
                'author' => 'Andy Weir',
                'purchaseUrl' => 'https://www.amazon.com/The-Martian-Andy-Weir/dp/0553418025'
            ]
        ]
    ?>

    <ul>
        <?php foreach ($books as $book) : ?>
            <li>
                <a href="<?= $book['purchaseUrl'] ?>">
                    <?= $book['name'] ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>

</html>