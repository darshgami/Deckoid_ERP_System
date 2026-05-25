<?php
require_once '../config/env.php';
require_once '../includes/database.php';
require_once '../includes/middleware.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');
requireAuth();

try {
    $db = Database::getInstance();
    $userId = $_SESSION['user_id'];
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        $stmt = $db->prepare("SELECT id, full_name, email, username, role, phone_number FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            throw new Exception('User not found');
        }
        
        ApiResponse::send(ApiResponse::success('Profile retrieved', ['user' => $user]));

    } elseif ($method === 'PUT') {
        // Update profile info
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (empty($input['full_name']) || empty($input['email'])) {
            throw new Exception('Full name and email are required');
        }

        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }

        $stmt = $db->prepare("UPDATE users SET full_name = ?, email = ?, phone_number = ? WHERE id = ?");
        $stmt->execute([$input['full_name'], $input['email'], $input['phone_number'] ?? null, $userId]);
        
        // Update session
        $_SESSION['full_name'] = $input['full_name'];
        
        ApiResponse::send(ApiResponse::success('Profile updated successfully'));

    } elseif ($method === 'PATCH') {
        // Change password
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (empty($input['current_password']) || empty($input['new_password'])) {
            throw new Exception('Both current and new passwords are required');
        }

        if ($input['new_password'] !== $input['confirm_password']) {
            throw new Exception('New passwords do not match');
        }

        if (strlen($input['new_password']) < 8) {
            throw new Exception('New password must be at least 8 characters');
        }

        // Verify current password
        $stmt = $db->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($input['current_password'], $user['password_hash'])) {
            throw new Exception('Current password is incorrect');
        }

        // Hash new password
        $newHash = password_hash($input['new_password'], PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->execute([$newHash, $userId]);
        
        ApiResponse::send(ApiResponse::success('Password updated successfully'));

    } else {
        http_response_code(405);
        ApiResponse::send(ApiResponse::error('Method not allowed'), 405);
    }

} catch (Exception $e) {
    ApiResponse::send(ApiResponse::error($e->getMessage()), 400);
}
