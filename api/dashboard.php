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

    // Get all stats in a single optimized query
    $statsQuery = "
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN lead_status = 'New' THEN 1 ELSE 0 END) as new_count,
            SUM(CASE WHEN lead_status = 'Follow-up' THEN 1 ELSE 0 END) as followup_count,
            SUM(CASE WHEN deal_status = 'Won' OR lead_status = 'Converted' THEN 1 ELSE 0 END) as converted_count,
            SUM(CASE WHEN deal_status = 'Lost' OR lead_status = 'Lost' THEN 1 ELSE 0 END) as lost_count,
            SUM(CASE WHEN payment_status = 'Pending' THEN 1 ELSE 0 END) as pending_payments,
            SUM(CASE WHEN next_followup_date = CURDATE() THEN 1 ELSE 0 END) as today_followups
        FROM leads";
    
    $statsRow = $db->query($statsQuery)->fetch(PDO::FETCH_ASSOC);
    
    $stats = [
        'total' => (int)($statsRow['total'] ?? 0),
        'new' => (int)($statsRow['new_count'] ?? 0),
        'followup' => (int)($statsRow['followup_count'] ?? 0),
        'converted' => (int)($statsRow['converted_count'] ?? 0),
        'lost' => (int)($statsRow['lost_count'] ?? 0),
        'pending_payments' => (int)($statsRow['pending_payments'] ?? 0),
        'today_followups' => (int)($statsRow['today_followups'] ?? 0)
    ];

    // Get recent leads
    $stmt = $db->query("SELECT lead_id, company_client_name, contact_person, mobile_number, lead_category, lead_status FROM leads ORDER BY created_at DESC LIMIT 5");
    $recentLeads = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get upcoming followups
    $stmt = $db->prepare("SELECT lead_id, company_client_name, contact_person, next_followup_date, last_followup_notes FROM leads WHERE next_followup_date >= CURDATE() ORDER BY next_followup_date ASC LIMIT 5");
    $stmt->execute();
    $upcomingFollowups = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get recent activity
    $stmt = $db->query("SELECT al.company_client_name, u.full_name as user_name, al.activity_type, al.created_at 
                        FROM lead_activity_logs al 
                        LEFT JOIN users u ON al.user_id = u.id 
                        ORDER BY al.created_at DESC LIMIT 10");
    $recentActivity = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Monthly Lead Graph (Last 6 months)
    $monthlyStats = [];
    $sixMonthsAgo = date('Y-m-01', strtotime('-5 months'));
    
    $stmt = $db->prepare("
        SELECT 
            DATE_FORMAT(created_at, '%b') as month_label,
            DATE_FORMAT(created_at, '%Y-%m') as month_key,
            COUNT(*) as count 
        FROM leads 
        WHERE created_at >= ? 
        GROUP BY month_key 
        ORDER BY month_key ASC
    ");
    $stmt->execute([$sixMonthsAgo]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $monthMap = [];
    foreach ($results as $row) {
        $monthMap[$row['month_key']] = $row;
    }
    
    for ($i = 5; $i >= 0; $i--) {
        $key = date('Y-m', strtotime("-$i months"));
        $label = date('M', strtotime("-$i months"));
        $monthlyStats[] = [
            'month' => $label,
            'count' => isset($monthMap[$key]) ? (int)$monthMap[$key]['count'] : 0
        ];
    }

    // Source of Lead Statistics
    $stmt = $db->query("SELECT source_of_lead as source, COUNT(*) as count FROM leads GROUP BY source_of_lead ORDER BY count DESC LIMIT 5");
    $sourceStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Assigned User Statistics
    $stmt = $db->query("SELECT u.full_name, COUNT(l.id) as count 
                        FROM users u 
                        LEFT JOIN leads l ON l.assigned_to = u.id 
                        GROUP BY u.id 
                        ORDER BY count DESC LIMIT 5");
    $userStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    ApiResponse::send(ApiResponse::success('Dashboard statistics retrieved successfully', [
        'stats' => $stats,
        'recent_leads' => $recentLeads,
        'upcoming_followups' => $upcomingFollowups,
        'recent_activity' => $recentActivity,
        'monthly_stats' => $monthlyStats,
        'source_stats' => $sourceStats,
        'user_stats' => $userStats
    ]));

} catch (Exception $e) {
    Logger::error('Dashboard API Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    ApiResponse::send(ApiResponse::error('Unable to load dashboard data. Please try again later.'), 500);
}