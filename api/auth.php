<?php

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/utils.php';

header('Content-Type: application/json');
apply_api_cors_headers('POST, GET, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

try {
    if ($method === 'POST') {
        $inputRaw = file_get_contents('php://input');
        $input = json_decode($inputRaw, true);

        // Debug: Log the raw input if it's not valid JSON
        if ($input === null && !empty($inputRaw)) {
            Logger::error('Invalid JSON received', ['raw_input' => substr($inputRaw, 0, 200), 'json_error' => json_last_error_msg()]);
            throw new Exception('Invalid request format. Please ensure you are sending valid JSON.');
        }

        // Ensure $input is an array
        $input = is_array($input) ? $input : [];

        if (strpos($path, '/register') !== false) {
            $result = AuthController::register($input);
            ApiResponse::send(ApiResponse::success($result['message'], ['user_id' => $result['user_id']]));
        } elseif (strpos($path, '/login') !== false) {
            $result = AuthController::login($input);
            ApiResponse::send(ApiResponse::success($result['message'], ['user' => $result['user']]));
        } elseif (strpos($path, '/logout') !== false) {
            $result = AuthController::logout();
            ApiResponse::send(ApiResponse::success($result['message']));
        } else {
            ApiResponse::send(ApiResponse::error('The requested endpoint was not found'), 404);
        }
    } elseif ($method === 'GET') {
        if (strpos($path, '/status') !== false) {
            ApiResponse::send(ApiResponse::success('Auth status retrieved', [
                'isLoggedIn' => AuthController::isLoggedIn(),
                'user' => [
                    'username' => $_SESSION['username'] ?? null,
                    'role' => $_SESSION['role'] ?? null
                ]
            ]));
        } else {
            ApiResponse::send(ApiResponse::error('The requested endpoint was not found'), 404);
        }
    } else {
        ApiResponse::send(ApiResponse::error('Method not allowed'), 405);
    }
} catch (Exception $e) {
    // Log the actual technical error internally
    Logger::error('Auth API Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    
    // Send a friendly message to the user
    $message = $e->getMessage();
    // Only allow specific "safe" messages to pass through, otherwise use a generic one
    $safeMessages = ['User already exists', 'Invalid credentials', 'Account is inactive', 'Missing required fields', 'Password must be at least 8 characters', 'All fields are required', 'Invalid email format', 'Username and password are required'];
    
    $userFriendlyMessage = in_array($message, $safeMessages) ? $message : 'Something went wrong while processing your request. Please try again.';
    
    ApiResponse::send(ApiResponse::error($userFriendlyMessage), 400);
}
