<?php

namespace Core\Middleware;

use Core\Middleware;
use Core\Response;
use Core\Session;

class GuestMiddleware extends Middleware
{
    protected function process($request = null): Response
    {
        // Start secure session if not already started
        Session::start();

        // Check if user is already authenticated
        if (Session::validate()) {
            // User is already logged in, redirect to home
            return new Response('', Response::REDIRECT, ['Location' => '/home']);
        }

        // User is not authenticated, continue to next middleware or controller
        return new Response('', Response::OK);
    }
}
