<?php

use Illuminate\Support\Collection;

require 'vendor/autoload.php';

$collection = new Collection([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

$r1 = $collection->filter(function ($item) {
    return $item % 2 === 0;
});

dd($r1);
