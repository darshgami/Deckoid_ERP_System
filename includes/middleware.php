<?php

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/utils.php';

/**
 * Route Protection Middleware
 */

function requireAuth() {
    // CSRF Protection for state-changing requests
    if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'OPTIONS') {
        $headers = getallheaders();
        $token = $headers['X-CSRF-TOKEN'] ?? $headers['X-Csrf-Token'] ?? '';
        
        // Skip CSRF check for login since user isn't authenticated yet
        $isLogin = isset($_POST['action']) && $_POST['action'] === 'login';
        $inputData = json_decode(file_get_contents('php://input'), true);
        if (!$isLogin && isset($inputData['action']) && $inputData['action'] === 'login') {
            $isLogin = true;
        }

        if (!$isLogin && !verify_csrf($token)) {
            if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Invalid CSRF token', 'message' => 'Invalid CSRF token']);
                exit;
            }
            header('Location: ../login.php');
            exit;
        }
    }

    if (!AuthController::isLoggedIn()) {
        if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Authentication required', 'message' => 'Authentication required. Please log in.']);
            exit;
        }
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
