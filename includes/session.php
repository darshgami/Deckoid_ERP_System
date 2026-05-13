<?php

require_once __DIR__ . '/../config/env.php';

/**
 * Secure Session Management Configuration
 * 
 * Implements security best practices for PHP sessions:
 * - HttpOnly: Prevents JavaScript access to session cookie
 * - Secure: Ensures cookies are only sent over HTTPS (if enabled)
 * - SameSite: Mitigates CSRF attacks
 * - Regeneration: Prevents session fixation
 */

function start_secure_session() {
    if (session_status() === PHP_SESSION_NONE) {
        $isHttps = (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off')
            || (!empty($_SERVER['SERVER_PORT']) && (string)$_SERVER['SERVER_PORT'] === '443')
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && stripos((string)$_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') !== false)
            || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower((string)$_SERVER['HTTP_X_FORWARDED_SSL']) === 'on');

        $sameSite = Env::get('SESSION_SAMESITE', 'Lax');
        $sameSite = ucfirst(strtolower((string)$sameSite));
        if (!in_array($sameSite, ['Lax', 'Strict', 'None'], true)) {
            $sameSite = 'Lax';
        }

        // Set session cookie parameters
        session_set_cookie_params([
            'lifetime' => 0, // Session cookie expires when browser closes
            'path' => '/',
            'domain' => '', // Use current domain
            'secure' => $isHttps,
            'httponly' => true,
            'samesite' => $sameSite
        ]);

        session_start();
    }
}

/**
 * Regenerate session ID to prevent session fixation
 */
function secure_session_regenerate() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}
