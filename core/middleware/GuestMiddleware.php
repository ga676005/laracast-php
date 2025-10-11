<?php

namespace Core\Middleware;

use Core\Middleware;
use Core\Response;
use Core\Security;

class GuestMiddleware extends Middleware
{
    protected function process($request = null): Response
    {
        // Start secure session if not already started
        Security::startSecureSession();

        // Check if user is already authenticated
        if (Security::validateSession()) {
            // User is already logged in, redirect to home
            return new Response('', Response::REDIRECT, ['Location' => '/home']);
        }

        // User is not authenticated, continue to next middleware or controller
        return new Response('', Response::OK);
    }
}
