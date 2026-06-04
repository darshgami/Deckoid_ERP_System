<?php
require_once '../config/env.php';
require_once '../includes/database.php';
require_once '../includes/middleware.php';
require_once '../includes/utils.php';

header('Content-Type: application/json');
apply_api_cors_headers('GET, POST, PUT, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

requireAuth();

$method = $_SERVER['REQUEST_METHOD'];

if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
    if (AuthController::getCurrentRole() === 'staff') {
        ApiResponse::send(ApiResponse::error('Access denied. Staff users have view-only permissions for Followups.'), 403);
    }
}

$db = Database::getInstance();

try {
    if ($method === 'GET') {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;

        $where = ["f.status != 'Completed'"]; // Exclude completed
        $params = [];

        if (AuthController::getCurrentRole() === 'staff') {
            $where[] = "l.assigned_to = ?";
            $params[] = $_SESSION['user_id'];
        }

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = '%' . $_GET['search'] . '%';
            $where[] = "(l.company LIKE ? OR l.contact_person LIKE ? OR l.mobile_number LIKE ?)";
            $params = array_merge($params, [$search, $search, $search]);
        }

        if (isset($_GET['followup_filter'])) {
            $today = date('Y-m-d');
            if ($_GET['followup_filter'] === 'today') {
                $where[] = "f.followup_date = ?";
                $params[] = $today;
            } elseif ($_GET['followup_filter'] === 'upcoming') {
                $where[] = "f.followup_date > ?";
                $params[] = $today;
            }
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Get total count
        $countStmt = $db->prepare("SELECT COUNT(*) as total FROM followups f LEFT JOIN leads l ON l.id = f.lead_id $whereClause");
        $countStmt->execute($params);
        $totalRow = $countStmt->fetch();
        $total = $totalRow ? $totalRow['total'] : 0;

        // Get followups
        $stmt = $db->prepare("SELECT f.*, l.company, l.contact_person, l.mobile_number, l.email_id, l.lead_category, l.lead_status, l.remarks as lead_remarks, u.full_name as assigned_to_name 
                              FROM followups f 
                              LEFT JOIN leads l ON l.id = f.lead_id 
                              LEFT JOIN users u ON l.assigned_to = u.id 
                              $whereClause 
                              ORDER BY f.followup_date ASC 
                              LIMIT ? OFFSET ?");
        
        $paramIndex = 1;
        foreach ($params as $val) {
            $stmt->bindValue($paramIndex++, $val);
        }
        $stmt->bindValue($paramIndex++, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue($paramIndex++, (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $followups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Map remarks properly. If followups table remarks is empty, fallback to leads remarks. Wait, the prompt says "Determine whether remarks are stored in: leads table OR followups table... If remarks exist in leads table: Modify API query... SELECT f.*, l.company, l.contact_person, l.remarks FROM followups f LEFT JOIN leads l ON l.id = f.lead_id"
        // And "REMARKS MUST NEVER BE NULL. If empty Return: '' Not undefined null *"
        foreach ($followups as &$f) {
            // Per prompt: SELECT f.*, ..., l.remarks. It will overwrite f.remarks if we aliased it, but let's just use l.remarks.
            $remarks = $f['lead_remarks'] ?? $f['remarks'];
            $f['remarks'] = $remarks === null ? '' : $remarks;
            unset($f['lead_remarks']); // Clean up
            
            // Map followup_date to next_followup_date to maintain UI compatibility, or we can update the UI.
            // Let's keep it as followup_date and update UI.
        }
        unset($f);

        ApiResponse::send(ApiResponse::success('Followups retrieved successfully', [
            'leads' => $followups, // Returning under 'leads' key so frontend JS doesn't break
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => (int)$total,
                'pages' => ceil($total / $limit)
            ]
        ]));
    }
} catch (Exception $e) {
    Logger::error("Followups API Error: " . $e->getMessage());
    ApiResponse::send(ApiResponse::error($e->getMessage()));
}
