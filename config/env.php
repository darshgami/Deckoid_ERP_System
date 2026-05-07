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
            throw new Exception('.env file not found at: ' . $path);
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

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