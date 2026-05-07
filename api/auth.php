<?php

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');
// Adjust CORS for session-based auth (Credentials must be true)
header('Access-Control-Allow-Origin: ' . ($_SERVER['HTTP_ORIGIN'] ?? '*'));
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
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
            $result = AuthController::register($input);
            echo json_encode($result);
        } elseif (strpos($path, '/login') !== false) {
            $result = AuthController::login($input);
            echo json_encode($result);
        } elseif (strpos($path, '/logout') !== false) {
            $result = AuthController::logout();
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
        }
    } elseif ($method === 'GET') {
        // Status check endpoint
        if (strpos($path, '/status') !== false) {
            echo json_encode([
                'isLoggedIn' => AuthController::isLoggedIn(),
                'user' => [
                    'username' => $_SESSION['username'] ?? null,
                    'role' => $_SESSION['role'] ?? null
                ]
            ]);
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