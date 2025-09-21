<?php 

$books = [
    [
        'name' => 'Do Androids Dream of Electric Sheep',
        'author' => 'Philip K. Dick',
        'releaseYear' => 1968,
        'purchaseUrl' => 'https://www.amazon.com/Do-Androids-Dream-Electric-Sheep/dp/0451524934'
    ],
    [
        'name' => 'Project Hail Mary',
        'author' => 'Andy Weir',
        'releaseYear' => 2021,
        'purchaseUrl' => 'https://www.amazon.com/Project-Hail-Mary-Andy-Weir/dp/1524759299'
    ],
    [
        'name' => 'The Martian',
        'author' => 'Andy Weir',
        'releaseYear' => 2012,
        'purchaseUrl' => 'https://www.amazon.com/The-Martian-Andy-Weir/dp/0553418025'
    ]
];

$searchAuthor = $_GET['author'] ?? '';
$searchReleaseYear = !empty($_GET['releaseYear']) ? (int)$_GET['releaseYear'] : '';
$filteredBooks = array_filter($books, function($book) use ($searchAuthor, $searchReleaseYear) {
    $authorMatch = empty($searchAuthor) || stripos($book['author'], $searchAuthor) !== false;
    $yearMatch = empty($searchReleaseYear) || $book['releaseYear'] >= $searchReleaseYear;
    return $authorMatch && $yearMatch;
});

$noBooksMessage = "";

// Add filter descriptions if any filters are active
$activeFilters = [];
if (!empty($searchAuthor)) {
    $activeFilters[] = "author containing '{$searchAuthor}'";
}
if (!empty($searchReleaseYear)) {
    $activeFilters[] = "release year >= {$searchReleaseYear}";
}

if (!empty($activeFilters)) {
    $noBooksMessage .= sprintf("No books found for %s.", implode(' and ', $activeFilters));
}


require "index.view.php";