<?php

namespace Core\Middleware;

use Core\Middleware;
use Core\Response;
use Core\Security;

class SessionAuthMiddleware extends Middleware
{
    protected function process($request = null): Response
    {
        // Start secure session if not already started
        Security::startSecureSession();

        // Validate session and user authentication
        if (!Security::validateSession()) {
            // Log the unauthorized access attempt
            error_log('Unauthorized session access attempt from IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));

            // Clear any existing session data
            Security::destroySession();

            // Redirect to signin page
            return new Response('', Response::REDIRECT, ['Location' => '/signin']);
        }

        // User is authenticated via session, continue to next middleware or controller
        return new Response('', Response::OK);
    }
}
