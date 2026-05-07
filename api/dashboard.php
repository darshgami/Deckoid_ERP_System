<?php

require_once '../config/env.php';
require_once '../includes/database.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Check authentication
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';
if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$token = $matches[1];
try {
    // For now, simple token validation (in production, properly validate JWT)
    // This is a placeholder - implement proper JWT validation

    $db = Database::getInstance();

    // Get statistics
    $stats = [
        'total' => 0,
        'hot' => 0,
        'warm' => 0,
        'cold' => 0
    ];

    $stmt = $db->query("SELECT lead_category, COUNT(*) as count FROM leads GROUP BY lead_category");
    while ($row = $stmt->fetch()) {
        $stats[strtolower($row['lead_category'])] = (int)$row['count'];
        $stats['total'] += (int)$row['count'];
    }

    // Get recent leads
    $stmt = $db->query("SELECT lead_id, company_client_name, contact_person, mobile_number, lead_category FROM leads ORDER BY created_at DESC LIMIT 5");
    $recentLeads = $stmt->fetchAll();

    echo json_encode([
        'stats' => $stats,
        'recent_leads' => $recentLeads
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}