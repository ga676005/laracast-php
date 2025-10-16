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

            // Build redirect URL with previous URL as query parameter (only for GET requests)
            $redirectUrl = '/signin';
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $currentUrl = $_SERVER['REQUEST_URI'] ?? '/';
                $redirectUrl .= '?previousurl=' . urlencode($currentUrl);
            }

            // Redirect to signin page
            return new Response('', Response::REDIRECT, ['Location' => $redirectUrl]);
        }

        // User is authenticated via session, continue to next middleware or controller
        return new Response('', Response::OK);
    }
}
