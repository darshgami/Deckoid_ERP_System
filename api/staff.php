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
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;

        $search = isset($_GET['search']) && trim($_GET['search']) !== '' ? trim($_GET['search']) : null;

        $where = '';
        $params = [];

        if ($search) {
            $where = 'WHERE full_name LIKE ? OR username LIKE ? OR email LIKE ?';
            $searchParam = '%' . $search . '%';
            $params = [$searchParam, $searchParam, $searchParam];
        }

        // Get total count
        $countStmt = $db->prepare("SELECT COUNT(*) FROM users $where");
        $countStmt->execute($params);
        $totalItems = (int)$countStmt->fetchColumn();
        $totalPages = ceil($totalItems / $limit);

        // Get paginated data
        $stmt = $db->prepare("SELECT id, full_name, email, username, role, status, last_login_at, created_at FROM users $where ORDER BY created_at DESC LIMIT ? OFFSET ?");
        
        $paramIndex = 1;
        foreach ($params as $param) {
            $stmt->bindValue($paramIndex++, $param);
        }
        $stmt->bindValue($paramIndex++, $limit, PDO::PARAM_INT);
        $stmt->bindValue($paramIndex++, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ApiResponse::send(ApiResponse::success('Staff list retrieved', [
            'users' => $users,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $totalItems,
                'pages' => $totalPages
            ]
        ]));

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
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (empty($input['id'])) {
            throw new Exception('Missing User ID');
        }

        // Check if this is a status-only update or a full update
        if (isset($input['status']) && count($input) === 2) {
            $stmt = $db->prepare("UPDATE users SET status = ? WHERE id = ?");
            $stmt->execute([$input['status'], $input['id']]);
            ApiResponse::send(ApiResponse::success('Status updated successfully'));
        } else {
            // Full update
            $validator = new Validator();
            $rules = [
                'full_name' => 'required|min:3',
                'username' => 'required|min:3',
                'email' => 'required|email',
                'role' => 'required'
            ];

            if (!$validator->validate($input, $rules)) {
                ApiResponse::send(ApiResponse::error($validator->getFirstError()), 400);
            }

            $sql = "UPDATE users SET full_name = ?, username = ?, email = ?, role = ? WHERE id = ?";
            $params = [$input['full_name'], $input['username'], $input['email'], $input['role'], $input['id']];
            
            // Handle optional password update
            if (!empty($input['password'])) {
                if (strlen($input['password']) < 8) {
                    throw new Exception('Password must be at least 8 characters');
                }
                $sql = "UPDATE users SET full_name = ?, username = ?, email = ?, role = ?, password_hash = ? WHERE id = ?";
                $params = [
                    $input['full_name'], 
                    $input['username'], 
                    $input['email'], 
                    $input['role'], 
                    password_hash($input['password'], PASSWORD_DEFAULT),
                    $input['id']
                ];
            }

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            ApiResponse::send(ApiResponse::success('Staff account updated successfully'));
        }

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
} catch (Throwable $e) {
    ApiResponse::send(ApiResponse::error($e->getMessage()), 400);
}
