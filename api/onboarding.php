<?php
header('Content-Type: application/json');
require_once '../includes/middleware.php';
require_once '../includes/database.php';
require_once '../includes/utils.php';

// Ensure user is logged in
requireAuth();

$db = Database::getInstance();
$method = $_SERVER['REQUEST_METHOD'];

if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
    if (AuthController::getCurrentRole() === 'staff') {
        ApiResponse::send(ApiResponse::error('Access denied. Staff users have view-only permissions for Onboarding.'), 403);
    }
}

try {
    if ($method === 'GET') {
        // List onboardings
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;

        $where = [];
        $params = [];

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = '%' . $_GET['search'] . '%';
            $where[] = "(co.project_name LIKE ? OR co.add_work LIKE ? OR l.company LIKE ?)";
            $params = array_merge($params, [$search, $search, $search]);
        }

        if (isset($_GET['status']) && !empty($_GET['status'])) {
            $where[] = "co.status = ?";
            $params[] = $_GET['status'];
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Count total
        $countStmt = $db->prepare("SELECT COUNT(*) as total FROM customer_onboarding co LEFT JOIN leads l ON co.lead_id = l.id $whereClause");
        $countStmt->execute($params);
        $total = $countStmt->fetch()['total'] ?? 0;

        // Fetch data
        $stmt = $db->prepare("SELECT co.*, l.company as lead_company, l.contact_person as lead_contact_person 
                              FROM customer_onboarding co 
                              LEFT JOIN leads l ON co.lead_id = l.id 
                              $whereClause 
                              ORDER BY co.created_at DESC 
                              LIMIT ? OFFSET ?");
        
        $paramIndex = 1;
        foreach ($params as $val) {
            $stmt->bindValue($paramIndex++, $val);
        }
        $stmt->bindValue($paramIndex++, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue($paramIndex++, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $onboardings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ApiResponse::send(ApiResponse::success('Onboardings retrieved', [
            'onboardings' => $onboardings,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => (int)$total,
                'pages' => ceil($total / $limit)
            ]
        ]));

    } elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);

        $validator = new Validator();
        $rules = [
            'lead_id' => 'required',
            'project_name' => 'required|min:3',
            'add_work' => 'required',
            'onboarding_date' => 'required'
        ];

        if (!$validator->validate($input, $rules)) {
            throw new Exception($validator->getFirstError());
        }

        $id = generateUUID();
        $stmt = $db->prepare("INSERT INTO customer_onboarding 
            (id, lead_id, project_name, company, contact_person, add_work, onboarding_date, status, remarks, created_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $id,
            $input['lead_id'],
            $input['project_name'],
            $input['company'] ?? null,
            $input['contact_person'] ?? null,
            $input['add_work'],
            $input['onboarding_date'],
            $input['status'] ?? 'Pending',
            $input['remarks'] ?? null,
            $_SESSION['user_id']
        ]);

        ApiResponse::send(ApiResponse::success('Onboarding created successfully', ['id' => $id]));

    } elseif ($method === 'PUT') {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $id = basename($path);
        }
        if (empty($id) || $id === 'onboarding.php') {
            throw new Exception('Onboarding ID required');
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if ($input === null) {
            throw new Exception('Invalid JSON payload provided');
        }
        
        $allowed = ['project_name', 'company', 'contact_person', 'add_work', 'onboarding_date', 'status', 'remarks'];
        $updates = [];
        $params = [];

        foreach ($allowed as $field) {
            if (array_key_exists($field, $input)) {
                $updates[] = "$field = ?";
                $params[] = $input[$field];
            }
        }

        if (empty($updates)) throw new Exception('No valid fields provided');

        $params[] = $id;
        $stmt = $db->prepare("UPDATE customer_onboarding SET " . implode(', ', $updates) . ", updated_at = NOW() WHERE id = ?");
        $stmt->execute($params);

        ApiResponse::send(ApiResponse::success('Onboarding updated successfully'));

    } elseif ($method === 'DELETE') {
        requireAdmin();
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $id = basename($path);
        }
        if (empty($id) || $id === 'onboarding.php') {
            throw new Exception('Onboarding ID required');
        }

        $stmt = $db->prepare("DELETE FROM customer_onboarding WHERE id = ?");
        $stmt->execute([$id]);

        ApiResponse::send(ApiResponse::success('Onboarding deleted successfully'));

    } else {
        ApiResponse::send(ApiResponse::error('Method not allowed'), 405);
    }
} catch (Throwable $e) {
    Logger::error('Onboarding API Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    ApiResponse::send(ApiResponse::error($e->getMessage()), 400);
}
