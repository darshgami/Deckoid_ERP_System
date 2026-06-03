<?php

require_once '../config/env.php';
require_once '../includes/database.php';

require_once '../includes/middleware.php';
require_once '../includes/utils.php';

header('Content-Type: application/json');
apply_api_cors_headers('GET, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::send(ApiResponse::error('Method not allowed'), 405);
}

// Ensure users are authenticated
requireAuth();

try {
    $db = Database::getInstance();

    // Get filters from GET
    $where = [];
    $params = [];

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = '%' . $_GET['search'] . '%';
        $where[] = "(company LIKE ? OR contact_person LIKE ? OR mobile_number LIKE ? OR email_id LIKE ? OR lead_id LIKE ?)";
        $params = array_merge($params, [$search, $search, $search, $search, $search]);
    }

    if (isset($_GET['category']) && !empty($_GET['category'])) {
        $where[] = "lead_category = ?";
        $params[] = $_GET['category'];
    }

    if (isset($_GET['lead_status']) && !empty($_GET['lead_status'])) {
        $where[] = "lead_status = ?";
        $params[] = $_GET['lead_status'];
    }

    // Date Range Validation & Filtering
    $dateFrom = $_GET['date_from'] ?? null;
    $dateTo = $_GET['date_to'] ?? null;

    if ($dateFrom && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)) {
        throw new Exception('Invalid start date format');
    }
    if ($dateTo && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
        throw new Exception('Invalid end date format');
    }

    if ($dateFrom && $dateTo && $dateTo < $dateFrom) {
        throw new Exception('End date cannot be earlier than start date');
    }

    if ($dateFrom) {
        $where[] = "lead_date >= ?";
        $params[] = $dateFrom;
    }
    if ($dateTo) {
        $where[] = "lead_date <= ?";
        $params[] = $dateTo;
    }

    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    // Get filtered leads
    $stmt = $db->prepare("SELECT * FROM leads $whereClause ORDER BY created_at DESC");
    $stmt->execute($params);
    $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($leads)) {
        ApiResponse::send(ApiResponse::error('No leads found to export'), 404);
    }

    // Set headers for CSV download
    $filename = 'leads_export_' . date('Y-m-d_H-i-s') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);

    // Create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');

    // Add UTF-8 BOM for Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Add headers matching the requested exact DB structure
    fputcsv($output, [
        'Lead ID', 'Lead Date', 'Company', 'Contact Person', 'Mobile Number', 
        'Email ID', 'City', 'State', 'Lead Category', 'Lead Status', 
        'Assigned To', 'Next Follow-up Date', 'Estimated Budget', 'Payment Status', 
        'Reference By', 'Remarks'
    ]);

    // Add data rows exactly as they appear in the database
    foreach ($leads as $lead) {
        // Get assigned user name
        $assignedTo = '-';
        if (!empty($lead['assigned_to'])) {
            $uStmt = $db->prepare("SELECT full_name FROM users WHERE id = ?");
            $uStmt->execute([$lead['assigned_to']]);
            $user = $uStmt->fetch();
            if ($user) $assignedTo = $user['full_name'];
        }
        
        // Date formatting
        $leadDate = !empty($lead['lead_date']) ? date('d-m-y', strtotime($lead['lead_date'])) : '';
        $nextFollowup = !empty($lead['next_followup_date']) ? date('d-m-y', strtotime($lead['next_followup_date'])) : '';

        fputcsv($output, [
            $lead['lead_id'],
            $leadDate,
            $lead['company'],
            $lead['contact_person'],
            $lead['mobile_number'],
            $lead['email_id'],
            $lead['city'],
            $lead['state'],
            $lead['lead_category'],
            $lead['lead_status'],
            $assignedTo,
            $nextFollowup,
            $lead['estimated_budget'],
            $lead['payment_status'],
            $lead['reference_by'],
            $lead['remarks']
        ]);
    }

    fclose($output);
    exit;

} catch (Throwable $e) {
    ApiResponse::send(ApiResponse::error($e->getMessage()), 500);
}
