<?php

namespace Core;

use Core\Response;

class Router
{
    private $routes = [];
    private $middleware = [];

    public function middleware($middleware)
    {
        // Accept both string and array
        $this->middleware = is_array($middleware) ? $middleware : [$middleware];

        return $this; // Return $this for chaining
    }

    public function get($uri, $controller)
    {
        $this->addRoute('GET', $uri, $controller, $this->middleware);
        $this->middleware = []; // Reset after use

        return $this;
    }

    public function post($uri, $controller)
    {
        $this->addRoute('POST', $uri, $controller, $this->middleware);
        $this->middleware = []; // Reset after use

        return $this;
    }

    public function put($uri, $controller)
    {
        $this->addRoute('PUT', $uri, $controller, $this->middleware);
        $this->middleware = []; // Reset after use

        return $this;
    }

    public function patch($uri, $controller)
    {
        $this->addRoute('PATCH', $uri, $controller, $this->middleware);
        $this->middleware = []; // Reset after use

        return $this;
    }

    public function delete($uri, $controller)
    {
        $this->addRoute('DELETE', $uri, $controller, $this->middleware);
        $this->middleware = []; // Reset after use

        return $this;
    }

    private function addRoute($method, $uri, $controller, $middleware = [])
    {
        $this->routes[$method][$uri] = [
            'controller' => $controller,
            'middleware' => $middleware,
        ];
    }

    private function getHttpMethod()
    {
        return strtoupper($_POST['_method'] ?? $_SERVER['REQUEST_METHOD']);
    }

    private function getMiddleware($middlewareName)
    {
        $middlewareMap = [
            // Web authentication (session-based)
            'session-auth' => \Core\Middleware\SessionAuthMiddleware::class,
            'guest' => \Core\Middleware\GuestMiddleware::class,
            'csrf' => \Core\Middleware\CsrfMiddleware::class,
            'admin' => \Core\Middleware\AdminMiddleware::class,

            // API authentication (token-based)
            'api-auth' => \Core\Middleware\ApiAuthMiddleware::class,
        ];

        if (!isset($middlewareMap[$middlewareName])) {
            throw new \Exception("Middleware '{$middlewareName}' not found");
        }

        return new $middlewareMap[$middlewareName]();
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
            $route = $this->routes[$method][$routeKey];

            // Execute middleware in order
            /** @var Middleware|null $middlewareChain */
            $middlewareChain = null;
            /** @var Middleware|null $previousMiddleware */
            $previousMiddleware = null;

            foreach ($route['middleware'] as $middlewareName) {
                $middleware = $this->getMiddleware($middlewareName);

                if ($middlewareChain === null) {
                    $middlewareChain = $middleware;
                } else {
                    $previousMiddleware->setNext($middleware);
                }

                $previousMiddleware = $middleware;
            }

            // Execute middleware chain
            if ($middlewareChain !== null) {
                $response = $middlewareChain->handle();

                // If middleware returned a redirect or error response, send it
                if ($response->getStatusCode() !== Response::OK) {
                    $response->send();

                    return;
                }
            }

            // Execute controller
            requireFromBase($route['controller']);
        } else {
            http_response_code(404);
            requireFromView('404.view.php');
        }
    }
}
