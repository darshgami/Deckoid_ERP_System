<?php

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/utils.php';

/**
 * Route Protection Middleware
 */

/**
 * Ensure user is authenticated
 */
function requireAuth() {
    if (!AuthController::isLoggedIn()) {
        // For API requests
        if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
            ApiResponse::send(ApiResponse::error('Authentication required. Please log in.'), 401);
            exit;
        }
        
        // For Page requests
        header('Location: ../login.php');
        exit;
    }
}

/**
 * Ensure user has admin role
 */
function requireAdmin() {
    requireAuth();
    if (AuthController::getCurrentRole() !== 'admin') {
        if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
            ApiResponse::send(ApiResponse::error('Access denied. Administrator privileges are required.'), 403);
            exit;
        }
        header('Location: /admin/dashboard.php');
        exit;
    }
}

/**
 * Ensure user has staff or admin role
 */
function requireStaff() {
    requireAuth();
    $role = AuthController::getCurrentRole();
    if ($role !== 'staff' && $role !== 'admin') {
        if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
            ApiResponse::send(ApiResponse::error('Access denied. Insufficient permissions.'), 403);
            exit;
        }
        header('Location: ../login.php');
        exit;
    }
}
