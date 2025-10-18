<?php

namespace Core\Middleware;

use Core\Log;
use Core\Middleware;
use Core\Response;
use Core\Session;

class AdminMiddleware extends Middleware
{
    protected function process($request = null): Response
    {
        // Start secure session if not already started
        Session::start();

        // Check if user is authenticated
        if (!Session::validate()) {
            // Log the unauthorized access attempt
            Log::warning('Unauthorized admin access attempt', [
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

        // Check if user has admin role
        $user = Session::getUser();
        if (!$user || $user['role'] !== 'admin') {
            // Log the unauthorized admin access attempt
            Log::error('Non-admin user attempted to access admin area', [
                'email' => $user['email'] ?? 'unknown',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            ]);

            // Return 403 Forbidden
            return new Response('Access denied. Admin privileges required.', Response::FORBIDDEN);
        }

        // User is authenticated and has admin role, continue
        return new Response('', Response::OK);
    }
}
