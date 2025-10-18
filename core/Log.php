<?php

namespace Core;

class Log
{
    /**
     * Get the error log file path
     * 
     * @return string
     */
    public static function getErrorLogPath()
    {
        // Define BASE_PATH if not already defined
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
        }

        $config = requireFromBase('config.php');
        $logFile = $config['logging']['error_log_path'] ?? null;

        // If configured, use that path
        if (!empty($logFile) && file_exists($logFile)) {
            return $logFile;
        }

        // Use application storage directory
        $logPath = BASE_PATH . 'storage/logs/php_errors.log';

        // Create storage/logs directory if it doesn't exist
        $logDir = dirname($logPath);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        // Create log file if it doesn't exist
        if (!file_exists($logPath)) {
            file_put_contents($logPath, '');
        }

        return $logPath;
    }

    /**
     * Configure PHP error logging settings
     * 
     * @return string
     */
    public static function configureErrorLogging()
    {
        $logPath = self::getErrorLogPath();

        // Configure PHP to write ALL errors to our custom log file
        ini_set('error_log', $logPath);
        ini_set('log_errors', '1');
        ini_set('display_errors', '0'); // Don't display errors on screen
        ini_set('error_reporting', E_ALL); // Log all error types

        return $logPath;
    }

    /**
     * Log a message with specified level and context
     * 
     * @param string $level
     * @param string $message
     * @param array $context
     * @return string
     */
    public static function logMessage($level, $message, $context = [])
    {
        // Format the log message with level (no timestamp - error_log adds its own)
        $formattedMessage = "[{$level}] {$message}";

        // Add context if provided
        if (!empty($context)) {
            $formattedMessage .= ' ' . json_encode($context);
        }

        // Write to log file (error_log will add timestamp automatically)
        error_log($formattedMessage);

        return $formattedMessage;
    }

    /**
     * Log an INFO level message
     * 
     * @param string $message
     * @param array $context
     * @return string
     */
    public static function info($message, $context = [])
    {
        return self::logMessage('INFO', $message, $context);
    }

    /**
     * Log a WARNING level message
     * 
     * @param string $message
     * @param array $context
     * @return string
     */
    public static function warning($message, $context = [])
    {
        return self::logMessage('WARNING', $message, $context);
    }

    /**
     * Log an ERROR level message
     * 
     * @param string $message
     * @param array $context
     * @return string
     */
    public static function error($message, $context = [])
    {
        return self::logMessage('ERROR', $message, $context);
    }

    /**
     * Log a DEBUG level message
     * 
     * @param string $message
     * @param array $context
     * @return string
     */
    public static function debug($message, $context = [])
    {
        return self::logMessage('DEBUG', $message, $context);
    }
}
