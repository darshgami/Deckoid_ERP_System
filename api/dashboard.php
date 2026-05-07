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

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Check authentication using middleware
requireAuth();

try {

    $db = Database::getInstance();

    // Get statistics
    $stats = [
        'total' => 0,
        'won' => 0,
        'lost' => 0,
        'new' => 0
    ];

    // Total leads
    $stmt = $db->query("SELECT COUNT(*) as count FROM leads");
    $stats['total'] = (int)$stmt->fetch()['count'];

    // Won leads
    $stmt = $db->query("SELECT COUNT(*) as count FROM leads WHERE deal_status = 'Won'");
    $stats['won'] = (int)$stmt->fetch()['count'];

    // Lost leads
    $stmt = $db->query("SELECT COUNT(*) as count FROM leads WHERE deal_status = 'Lost'");
    $stats['lost'] = (int)$stmt->fetch()['count'];

    // New leads
    $stmt = $db->query("SELECT COUNT(*) as count FROM leads WHERE lead_status = 'New'");
    $stats['new'] = (int)$stmt->fetch()['count'];

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