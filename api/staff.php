<?php
require_once '../config/env.php';
require_once '../includes/database.php';
require_once '../includes/middleware.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');
requireAuth();

// Ensure only admins can access staff management
requireAdmin();

try {
    $db = Database::getInstance();
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        $stmt = $db->query("SELECT id, full_name, email, username, role, status, last_login_at, created_at FROM users ORDER BY created_at DESC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ApiResponse::send(ApiResponse::success('Staff list retrieved', ['users' => $users]));

    } elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            ApiResponse::send(ApiResponse::error('Invalid input data'), 400);
        }

        // Professional Validation
        $validator = new Validator();
        $rules = [
            'full_name' => 'required|min:3',
            'username' => 'required|min:3',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'role' => 'required'
        ];

        if (!$validator->validate($input, $rules)) {
            ApiResponse::send(ApiResponse::error($validator->getFirstError()), 400);
        }

        // Register user via AuthController
        $result = AuthController::register($input);
        ApiResponse::send(ApiResponse::success('Staff account created successfully', ['user_id' => $result['user_id']]));

    } elseif ($method === 'PUT') {
        // Update status
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (empty($input['id']) || empty($input['status'])) {
            throw new Exception('Missing ID or status');
        }

        $stmt = $db->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->execute([$input['status'], $input['id']]);
        echo json_encode(['message' => 'Status updated successfully']);

    } elseif ($method === 'DELETE') {
        $id = $_GET['id'] ?? null;
        if (!$id) throw new Exception('User ID is required');
        
        // Prevent self-deletion
        if ($id === $_SESSION['user_id']) {
            throw new Exception('You cannot delete your own account.');
        }

        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        ApiResponse::send(ApiResponse::success('Staff account deleted successfully'));

    } else {
        http_response_code(405);
        ApiResponse::send(ApiResponse::error('Method not allowed'), 405);
    }
} catch (Exception $e) {
    ApiResponse::send(ApiResponse::error($e->getMessage()), 400);
}
