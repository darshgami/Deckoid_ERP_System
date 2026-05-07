<?php

require_once __DIR__ . '/auth.php';

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
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized. Please login.']);
            exit;
        }
        
        // For Page requests
        header('Location: /login.php');
        exit;
    }
}

/**
 * Ensure user has admin role
 */
function requireAdmin() {
    requireAuth();
    if (AuthController::getCurrentRole() !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden. Admin access required.']);
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
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden. Access denied.']);
        exit;
    }
}
