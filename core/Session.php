<?php

namespace Core;

class Session
{
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Set secure session parameters
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
            ini_set('session.use_strict_mode', 1);
            ini_set('session.cookie_samesite', 'Strict');

            session_start();

            // Regenerate session ID for security
            self::regenerateId();
        }
    }

    public static function regenerateId()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public static function destroy()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            session_destroy();
        }
    }

    public static function validate()
    {
        if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
            return false;
        }

        // Check session timeout (30 minutes)
        $timeout = 30 * 60; // 30 minutes in seconds
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
            self::destroy();
            return false;
        }

        // Update last activity
        $_SESSION['last_activity'] = time();

        return true;
    }

    public static function setUser($userData)
    {
        $_SESSION['user'] = $userData;
        $_SESSION['last_activity'] = time();
    }

    public static function getUser()
    {
        return $_SESSION['user'] ?? null;
    }

    public static function isLoggedIn()
    {
        return isset($_SESSION['user']) && is_array($_SESSION['user']);
    }

    // Flash message methods
    public static function flash($key, $value = null)
    {
        // Ensure $_SESSION['_flash'] is always an array
        if (!isset($_SESSION['_flash']) || !is_array($_SESSION['_flash'])) {
            $_SESSION['_flash'] = [];
        }
        
        if ($value === null) {
            // Get and remove flash message
            $message = $_SESSION['_flash'][$key] ?? null;
            unset($_SESSION['_flash'][$key]);
            return $message;
        }
        
        // Set flash message
        $_SESSION['_flash'][$key] = $value;
    }

    public static function flashExists($key)
    {
        return isset($_SESSION['_flash']) && is_array($_SESSION['_flash']) && isset($_SESSION['_flash'][$key]);
    }

    public static function flashAll()
    {
        $messages = $_SESSION['_flash'] ?? [];
        $_SESSION['_flash'] = [];
        return $messages;
    }

    // Generic session data methods
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has($key)
    {
        return isset($_SESSION[$key]);
    }

    public static function remove($key)
    {
        unset($_SESSION[$key]);
    }
}
