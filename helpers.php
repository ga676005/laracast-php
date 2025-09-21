<?php 

function d($value) {
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
}

function dd($value) {
    d($value);
    die();
}

