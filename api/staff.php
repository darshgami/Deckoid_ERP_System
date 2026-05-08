<?php
require_once '../config/env.php';
require_once '../includes/database.php';
require_once '../includes/middleware.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');
requireAuth();

// Admin Only
if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

$db = Database::getInstance();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        // List all users except current admin
        $stmt = $db->prepare("SELECT id, full_name, username, role, status, last_login_at FROM users WHERE id != ? ORDER BY created_at DESC");
        $stmt->execute([$_SESSION['user_id']]);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['users' => $users]);

    } elseif ($method === 'POST') {
        // Add new staff
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (empty($input['full_name']) || empty($input['username']) || empty($input['password'])) {
            throw new Exception('Missing required fields');
        }

        // Mock a data array for AuthController::register
        $data = [
            'full_name' => $input['full_name'],
            'email' => $input['username'] . '@deckoid.local', // Placeholder as required no email field but controller needs it
            'username' => $input['username'],
            'password' => $input['password'],
            'role' => $input['role'] ?? 'staff'
        ];

        $result = AuthController::register($data);
        echo json_encode(['message' => 'Staff account created successfully', 'id' => $result['user_id']]);

    } elseif ($method === 'PUT') {
        // Update status
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (empty($input['id']) || empty($input['status'])) {
            throw new Exception('Missing ID or status');
        }

        $stmt = $db->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->execute([$input['status'], $input['id']]);
        echo json_encode(['message' => 'Status updated successfully']);

    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
