<?php

class Router {
    private $routes = [];
    
    public function get($uri, $controller) {
        $this->routes[$uri] = $controller;
    }
    
    public static function url($path = '') {
        // Get the directory name from the script path
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        
        // Remove leading slash from path if present
        $path = ltrim($path, '/');
        
        // Combine base path with the provided path
        return rtrim($scriptDir . '/' . $path, '/');
    }
    
    public function resolve() {
        $uri = parse_url($_SERVER['REQUEST_URI'])['path'];
        
        // Remove the project directory from the URI
        $projectDir = dirname($_SERVER['SCRIPT_NAME']);
        if (str_starts_with($uri, $projectDir)) {
            $uri = substr($uri, strlen($projectDir));
        }
        
        // Remove .php extension if present for routing
        $routeKey = $uri;
        if (str_ends_with($uri, '.php')) {
            $routeKey = substr($uri, 0, -4);
        }
        
        if (array_key_exists($routeKey, $this->routes)) {
            require $this->routes[$routeKey];
        } else {
            http_response_code(404);
            require 'views/404.view.php';
        }
    }
}