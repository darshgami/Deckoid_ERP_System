<?php

/**
 * Environment Configuration Loader
 * Loads and validates environment variables from .env file
 */
class Env
{
    private static $loaded = false;
    private static $env = [];

    /**
     * Load environment variables from .env file
     */
    public static function load($path = null)
    {
        if (self::$loaded) {
            return;
        }

        $path = $path ?: __DIR__ . '/../.env';

        if (!file_exists($path)) {
            // If .env is missing, we can't do much, but we shouldn't crash if we have defaults
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }

            if (strpos($line, '=') === false) continue;

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Remove quotes if present
            if (preg_match('/^["\'](.*)["\']$/', $value, $matches)) {
                $value = $matches[1];
            }

            self::$env[$name] = $value;
        }

        self::$loaded = true;
    }

    /**
     * Get environment variable
     */
    public static function get($key, $default = null)
    {
        if (!self::$loaded) {
            self::load();
        }

        return self::$env[$key] ?? $default;
    }

    /**
     * Get required environment variable
     */
    public static function require($key)
    {
        $value = self::get($key);

        if ($value === null) {
            throw new Exception("Required environment variable '{$key}' is not set");
        }

        return $value;
    }
}

// Load environment on include
Env::load();

// Configure Environment-based error handling
$isLocal = (Env::get('APP_ENV', 'production') === 'local');
$isDebug = (filter_var(Env::get('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN));

if ($isLocal && $isDebug) {
    // Development Settings: Show everything
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    // Production Settings: Hide everything from user
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
}

// Always log errors in both environments
ini_set('log_errors', 1);
$logDir = __DIR__ . '/../logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}
ini_set('error_log', $logDir . '/php_errors.log');

// Set System Timezone
date_default_timezone_set(Env::get('TIMEZONE', 'Asia/Kolkata'));