<?php

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
        // Set session cookie parameters
        session_set_cookie_params([
            'lifetime' => 0, // Session cookie expires when browser closes
            'path' => '/',
            'domain' => '', // Use current domain
            'secure' => isset($_SERVER['HTTPS']), // True if HTTPS is on
            'httponly' => true,
            'samesite' => 'Strict'
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
