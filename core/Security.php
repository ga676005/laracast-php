<?php

namespace Core;

use Core\Session;

class Security
{
    public static function generateCsrfToken()
    {
        $token = Session::get('csrf_token');
        if (!$token) {
            $token = bin2hex(random_bytes(32));
            Session::set('csrf_token', $token);
        }

        return $token;
    }

    public static function validateCsrfToken($token)
    {
        $storedToken = Session::get('csrf_token');
        return $storedToken && hash_equals($storedToken, $token);
    }


    public static function sanitizeInput($input)
    {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }

        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }
}
