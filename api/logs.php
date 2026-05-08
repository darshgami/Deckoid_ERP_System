<?php

require_once '../config/env.php';
require_once '../includes/database.php';
require_once '../includes/auth.php';
require_once '../includes/middleware.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . ($_SERVER['HTTP_ORIGIN'] ?? '*'));
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

requireAuth();

try {
    $db = Database::getInstance();
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        $page  = isset($_GET['page'])  ? (int)$_GET['page']  : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        $offset = ($page - 1) * $limit;

        // Optional filters
        $type   = isset($_GET['type'])   && $_GET['type']   !== '' ? $_GET['type']   : null;
        $search = isset($_GET['search']) && $_GET['search'] !== '' ? $_GET['search'] : null;

        $where  = [];
        $params = [];

        if ($type) {
            $where[]  = 'al.activity_type = ?';
            $params[] = $type;
        }

        if ($search) {
            $where[]  = '(COALESCE(l.company_client_name, al.company_client_name, al.notes) LIKE ?)';
            $params[] = '%' . $search . '%';
        }

        $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // Total count
        $countParams = $params;
        $totalStmt = $db->prepare("
            SELECT COUNT(*) 
            FROM lead_activity_logs al
            LEFT JOIN leads l ON al.lead_id = l.id
            LEFT JOIN users u ON al.user_id = u.id
            $whereSQL
        ");
        $totalStmt->execute($countParams);
        $total = (int)$totalStmt->fetchColumn();

        // Fetch logs — LEFT JOIN so rows survive even after lead deletion
        $fetchParams = array_merge($params, [$limit, $offset]);
        $stmt = $db->prepare("
            SELECT 
                al.id,
                al.activity_type,
                al.notes,
                al.created_at,
                COALESCE(l.company_client_name, al.company_client_name, 'Deleted Lead') AS company_client_name,
                COALESCE(u.full_name, 'System') AS user_name
            FROM lead_activity_logs al
            LEFT JOIN leads l ON al.lead_id = l.id
            LEFT JOIN users u ON al.user_id = u.id
            $whereSQL
            ORDER BY al.created_at DESC
            LIMIT ? OFFSET ?
        ");

        $i = 1;
        foreach ($params as $p) {
            $stmt->bindValue($i++, $p);
        }
        $stmt->bindValue($i++, $limit,  PDO::PARAM_INT);
        $stmt->bindValue($i++, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'data' => $logs,
            'pagination' => [
                'total' => $total,
                'page'  => $page,
                'limit' => $limit,
                'pages' => $total > 0 ? (int)ceil($total / $limit) : 1
            ]
        ]);

    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
