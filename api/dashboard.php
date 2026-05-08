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
        'new' => 0,
        'followup' => 0,
        'converted' => 0,
        'lost' => 0,
        'pending_payments' => 0,
        'today_followups' => 0
    ];

    // Total leads
    $stmt = $db->query("SELECT COUNT(*) as count FROM leads");
    $stats['total'] = (int)$stmt->fetch()['count'];

    // New leads
    $stmt = $db->query("SELECT COUNT(*) as count FROM leads WHERE lead_status = 'New'");
    $stats['new'] = (int)$stmt->fetch()['count'];

    // Followup leads
    $stmt = $db->query("SELECT COUNT(*) as count FROM leads WHERE lead_status = 'Follow-up'");
    $stats['followup'] = (int)$stmt->fetch()['count'];

    // Converted leads (Won)
    $stmt = $db->query("SELECT COUNT(*) as count FROM leads WHERE deal_status = 'Won' OR lead_status = 'Converted'");
    $stats['converted'] = (int)$stmt->fetch()['count'];

    // Lost leads
    $stmt = $db->query("SELECT COUNT(*) as count FROM leads WHERE deal_status = 'Lost' OR lead_status = 'Lost'");
    $stats['lost'] = (int)$stmt->fetch()['count'];

    // Pending Payments
    $stmt = $db->query("SELECT COUNT(*) as count FROM leads WHERE payment_status = 'Pending'");
    $stats['pending_payments'] = (int)$stmt->fetch()['count'];

    // Today's Followups
    $today = date('Y-m-d');
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM leads WHERE next_followup_date = ?");
    $stmt->execute([$today]);
    $stats['today_followups'] = (int)$stmt->fetch()['count'];

    // Get recent leads
    $stmt = $db->query("SELECT lead_id, company_client_name, contact_person, mobile_number, lead_category, lead_status FROM leads ORDER BY created_at DESC LIMIT 5");
    $recentLeads = $stmt->fetchAll();

    // Get upcoming followups
    $stmt = $db->prepare("SELECT lead_id, company_client_name, contact_person, next_followup_date, last_followup_notes FROM leads WHERE next_followup_date >= ? ORDER BY next_followup_date ASC LIMIT 5");
    $stmt->execute([$today]);
    $upcomingFollowups = $stmt->fetchAll();

    // Get recent activity
    $stmt = $db->query("SELECT l.company_client_name, u.full_name as user_name, al.activity_type, al.created_at 
                        FROM lead_activity_logs al 
                        JOIN leads l ON al.lead_id = l.id 
                        LEFT JOIN users u ON al.user_id = u.id 
                        ORDER BY al.created_at DESC LIMIT 10");
    $recentActivity = $stmt->fetchAll();

    // Monthly Lead Graph (Last 6 months)
    $monthlyStats = [];
    for ($i = 5; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $monthLabel = date('M', strtotime("-$i months"));
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM leads WHERE DATE_FORMAT(created_at, '%Y-%m') = ?");
        $stmt->execute([$month]);
        $monthlyStats[] = [
            'month' => $monthLabel,
            'count' => (int)$stmt->fetch()['count']
        ];
    }

    // Source of Lead Statistics
    $stmt = $db->query("SELECT source_of_lead as source, COUNT(*) as count FROM leads GROUP BY source_of_lead ORDER BY count DESC LIMIT 5");
    $sourceStats = $stmt->fetchAll();

    // Assigned User Statistics
    $stmt = $db->query("SELECT u.full_name, COUNT(l.id) as count 
                        FROM users u 
                        LEFT JOIN leads l ON l.assigned_to = u.id 
                        GROUP BY u.id 
                        ORDER BY count DESC LIMIT 5");
    $userStats = $stmt->fetchAll();

    echo json_encode([
        'stats' => $stats,
        'recent_leads' => $recentLeads,
        'upcoming_followups' => $upcomingFollowups,
        'recent_activity' => $recentActivity,
        'monthly_stats' => $monthlyStats,
        'source_stats' => $sourceStats,
        'user_stats' => $userStats
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}