<?php

namespace Core\Middleware;

use Core\Middleware;
use Core\Response;

class ApiAuthMiddleware extends Middleware
{
    protected function process($request = null): Response
    {
        // Get API token from Authorization header or X-API-Key header
        $apiToken = $this->getApiToken();

        if (!$apiToken) {
            return new Response('API token required', Response::UNAUTHORIZED);
        }

        // Validate API token (you'd store these in database)
        if (!$this->validateApiToken($apiToken)) {
            error_log('Invalid API token from IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));

            return new Response('Invalid API token', Response::UNAUTHORIZED);
        }

        // Set user context from API token (you'd look this up in database)
        $this->setUserFromToken($apiToken);

        return new Response('', Response::OK);
    }

    private function getApiToken()
    {
        // Check Authorization header: "Bearer your-token-here"
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }

        // Check X-API-Key header
        return $_SERVER['HTTP_X_API_KEY'] ?? null;
    }

    private function validateApiToken($token)
    {
        // In real implementation, check against database
        // For demo, we'll use a simple check
        $validTokens = [
            'user123-api-token-abc' => ['user_id' => 1, 'email' => 'user1@example.com'],
            'user456-api-token-def' => ['user_id' => 2, 'email' => 'user2@example.com'],
        ];

        return isset($validTokens[$token]);
    }

    private function setUserFromToken($token)
    {
        // Set user context for API requests (similar to session)
        $validTokens = [
            'user123-api-token-abc' => ['user_id' => 1, 'email' => 'user1@example.com'],
            'user456-api-token-def' => ['user_id' => 2, 'email' => 'user2@example.com'],
        ];

        if (isset($validTokens[$token])) {
            $_SESSION['user'] = $validTokens[$token];
        }
    }
}
