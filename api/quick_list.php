<?php
header('Content-Type: application/json');
require_once '../includes/middleware.php';
require_once '../includes/database.php';
require_once '../includes/utils.php';

$db = Database::getInstance();
requireAuth();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 25;
$offset = ($page - 1) * $limit;

try {
    $search = isset($_GET['search']) && trim($_GET['search']) !== '' ? trim($_GET['search']) : null;
    
    $whereClause = '';
    $params = [];

    if ($search) {
        $whereClause = "WHERE company LIKE ? OR contact_person LIKE ? OR mobile_number LIKE ? OR email_id LIKE ? OR lead_id LIKE ?";
        $searchParam = '%' . $search . '%';
        $params = [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam];
    }

    // Fetch only required fields for performance
    $stmt = $db->prepare("SELECT lead_id, company, mobile_number, remarks, next_followup_date FROM leads $whereClause ORDER BY created_at DESC LIMIT ? OFFSET ?");
    
    $paramIndex = 1;
    foreach ($params as $param) {
        $stmt->bindValue($paramIndex++, $param);
    }
    $stmt->bindValue($paramIndex++, $limit, PDO::PARAM_INT);
    $stmt->bindValue($paramIndex++, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total count
    $countStmt = $db->prepare("SELECT COUNT(*) FROM leads $whereClause");
    $countStmt->execute($params);
    $total = $countStmt->fetchColumn();

    ApiResponse::send(ApiResponse::success('Quick list retrieved', [
        'leads' => $leads,
        'pagination' => [
            'total' => (int)$total,
            'page' => $page,
            'pages' => ceil($total / $limit),
            'limit' => $limit
        ]
    ]));
} catch (Throwable $e) {
    ApiResponse::send(ApiResponse::error($e->getMessage()), 500);
}
?>
