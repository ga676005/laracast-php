<?php

use Core\Response;
use Core\Router;

function d($value)
{
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
}
function dd($value)
{
    d($value);
    die();
}

function authorize($condition, $statusCode = Response::FORBIDDEN)
{
    if (!$condition) {
        Router::push($statusCode);
        exit;
    }
}

function requireFromBase($path, $variables = [])
{
    $fullPath = BASE_PATH . $path;
    if (file_exists($fullPath)) {
        extract($variables);

        return require $fullPath;
    } else {
        throw new Exception("File not found: {$fullPath}");
    }
}

function requireFromView($viewPath, $variables = [])
{
    $fullPath = BASE_PATH . 'views/' . $viewPath;
    if (file_exists($fullPath)) {
        // Extract variables to make them available in the view
        extract($variables);

        return require $fullPath;
    } else {
        throw new Exception("View file not found: {$fullPath}");
    }
}
