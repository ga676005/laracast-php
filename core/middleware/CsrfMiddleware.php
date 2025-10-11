<?php

namespace Core\Middleware;

use Core\Middleware;
use Core\Response;
use Core\Security;

class CsrfMiddleware extends Middleware
{
    protected function process($request = null): Response
    {
        // Only validate CSRF for POST, PUT, PATCH, DELETE requests
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

            if (!Security::validateCsrfToken($token)) {
                // Log CSRF violation attempt
                error_log('CSRF token validation failed from IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));

                // Return 403 Forbidden
                return new Response('CSRF token mismatch', Response::FORBIDDEN);
            }
        }

        // CSRF validation passed, continue
        return new Response('', Response::OK);
    }
}
