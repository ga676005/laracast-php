<?php

namespace Core\Middleware;

use Core\Middleware;
use Core\Response;
use Core\Security;

class AdminMiddleware extends Middleware
{
    protected function process($request = null): Response
    {
        // Start secure session if not already started
        Security::startSecureSession();

        // Check if user is authenticated
        if (!Security::validateSession()) {
            // Log the unauthorized access attempt
            logWarning('Unauthorized admin access attempt', [
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            ]);

            // Clear any existing session data
            Security::destroySession();

            // Redirect to signin page
            return new Response('', Response::REDIRECT, ['Location' => '/signin']);
        }

        // Check if user has admin role
        if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
            // Log the unauthorized admin access attempt
            logError('Non-admin user attempted to access admin area', [
                'email' => $_SESSION['user']['email'] ?? 'unknown',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            ]);

            // Return 403 Forbidden
            return new Response('Access denied. Admin privileges required.', Response::FORBIDDEN);
        }

        // User is authenticated and has admin role, continue
        return new Response('', Response::OK);
    }
}
