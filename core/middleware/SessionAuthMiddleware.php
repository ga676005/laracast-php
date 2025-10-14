<?php

namespace Core\Middleware;

use Core\Middleware;
use Core\Response;
use Core\Session;

class SessionAuthMiddleware extends Middleware
{
    protected function process($request = null): Response
    {
        // Start secure session if not already started
        Session::start();

        // Validate session and user authentication
        if (!Session::validate()) {
            // Log the unauthorized access attempt
            logWarning('Unauthorized session access attempt', [
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            ]);

            // Clear any existing session data
            Session::destroy();

            // Redirect to signin page
            return new Response('', Response::REDIRECT, ['Location' => '/signin']);
        }

        // User is authenticated via session, continue to next middleware or controller
        return new Response('', Response::OK);
    }
}
