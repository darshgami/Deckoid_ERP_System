<?php

require_once '../config/env.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

try {
    if ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);

        if (strpos($path, '/register') !== false) {
            // Register endpoint
            $result = AuthController::register($input);
            echo json_encode($result);
        } elseif (strpos($path, '/login') !== false) {
            // Login endpoint
            $result = AuthController::login($input);
            echo json_encode($result);
        } elseif (strpos($path, '/logout') !== false) {
            // Logout endpoint
            $result = AuthController::logout($input);
            echo json_encode($result);
        } elseif (strpos($path, '/refresh') !== false) {
            // Refresh token endpoint
            $result = AuthController::refresh($input);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
        }
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}