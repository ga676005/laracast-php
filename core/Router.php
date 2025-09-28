<?php

namespace Core;

class Router
{
    private $routes = [];

    public function get($uri, $controller)
    {
        $this->addRoute('GET', $uri, $controller);
    }

    public function post($uri, $controller)
    {
        $this->addRoute('POST', $uri, $controller);
    }

    public function put($uri, $controller)
    {
        $this->addRoute('PUT', $uri, $controller);
    }

    public function patch($uri, $controller)
    {
        $this->addRoute('PATCH', $uri, $controller);
    }

    public function delete($uri, $controller)
    {
        $this->addRoute('DELETE', $uri, $controller);
    }

    private function addRoute($method, $uri, $controller)
    {
        $this->routes[$method][$uri] = $controller;
    }

    private function getHttpMethod()
    {
        return strtoupper($_POST['_method'] ?? $_SERVER['REQUEST_METHOD']);
    }

    public static function push($uri)
    {
        // Ensure the URI starts with a slash
        if (!str_starts_with($uri, '/')) {
            $uri = '/' . $uri;
        }

        // Redirect to the specified URI (like an <a> tag)
        header('Location: ' . self::url($uri));
        exit;
    }


    public static function url($path = '')
    {
        // Get the directory name from the script path
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);

        // If the script is in the root directory, the base path is empty
        $basePath = ($scriptDir === '/' || $scriptDir === '\\') ? '' : $scriptDir;

        // Remove leading slash from path to avoid double slashes
        $path = ltrim($path, '/');

        // Combine base path with the provided path
        $url = rtrim($basePath . '/' . $path, '/');

        // If the URL is empty (which happens for the root), default to '/'
        return $url === '' ? '/' : $url;
    }

    public function resolve($uri = null)
    {
        if (is_numeric($uri) && file_exists(BASE_PATH . 'views/' . $uri . '.view.php')) {
            // Check if the route is a status code and corresponding view file exists
            http_response_code($uri);
            requireFromView($uri . '.view.php');
            exit; // Status codes should stop execution
        }

        if ($uri === null) {
            $uri = parse_url($_SERVER['REQUEST_URI'])['path'];
        }

        // Get the HTTP method with support for method override
        $method = $this->getHttpMethod();

        // Remove the project directory from the URI if not in root
        $projectDir = dirname($_SERVER['SCRIPT_NAME']);
        if ($projectDir !== '/' && $projectDir !== '\\' && str_starts_with($uri, $projectDir)) {
            $uri = substr($uri, strlen($projectDir));
        }

        if ($uri === '' || $uri[0] !== '/') {
            $uri = '/' . $uri;
        }

        // Remove .php extension if present for routing
        $routeKey = $uri;
        if (str_ends_with($uri, '.php')) {
            $routeKey = substr($uri, 0, -4);
        }

        // Make router available to all included files
        $router = $this;

        // Check if route exists for the specific HTTP method
        if (isset($this->routes[$method]) && array_key_exists($routeKey, $this->routes[$method])) {
            requireFromBase($this->routes[$method][$routeKey]);
        } else {
            http_response_code(404);
            requireFromView('404.view.php');
        }
    }
}
