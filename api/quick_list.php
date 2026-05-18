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
    // Fetch only required fields for performance
    $stmt = $db->prepare("SELECT lead_id, company_client_name, mobile_number, remarks_notes FROM leads ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total count
    $countStmt = $db->query("SELECT COUNT(*) FROM leads");
    $total = $countStmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'data' => [
            'leads' => $leads,
            'pagination' => [
                'total' => (int)$total,
                'page' => $page,
                'pages' => ceil($total / $limit),
                'limit' => $limit
            ]
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
