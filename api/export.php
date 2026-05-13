<?php

require_once '../config/env.php';
require_once '../includes/database.php';

require_once '../includes/middleware.php';

header('Content-Type: application/json');
apply_api_cors_headers('GET, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Ensure only admins can export data
requireAdmin();

try {
    $db = Database::getInstance();

    // Get filters from GET
    $where = [];
    $params = [];

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = '%' . $_GET['search'] . '%';
        $where[] = "(company_client_name LIKE ? OR contact_person LIKE ? OR mobile_number LIKE ? OR email_id LIKE ? OR lead_id LIKE ?)";
        $params = array_merge($params, [$search, $search, $search, $search, $search]);
    }

    if (isset($_GET['category']) && !empty($_GET['category'])) {
        $where[] = "lead_category = ?";
        $params[] = $_GET['category'];
    }

    if (isset($_GET['status']) && !empty($_GET['status'])) {
        $where[] = "lead_status = ?";
        $params[] = $_GET['status'];
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
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['error' => 'No leads found to export']);
        exit;
    }

    // Set headers for CSV download
    $filename = 'leads_export_' . date('Y-m-d_H-i-s') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);

    // Create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');

    // Add UTF-8 BOM for Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Add headers matching Excel exactly
    fputcsv($output, [
        'Lead ID', 'Lead Date', 'Company / Client Name', 'Contact Person', 'Mobile Number', 
        'Alternative Number', 'Email ID', 'City', 'State', 'Source of Lead', 
        'Service Interested In', 'Lead Category', 'Lead Status', 'Priority', 
        'Assigned To', 'Next Follow-up Date', 'Last Follow-up Notes', 'Requirement Details', 
        'Estimated Budget', 'Proposal Sent', 'Meeting Scheduled', 'Quotation Sent', 
        'Deal Status', 'Expected Closing Date', 'Payment Status', 'Client Onboard Date', 
        'Project Start Date', 'Project Status', 'Reference By', 'Website / Social Link', 'Remarks / Notes'
    ]);

    // Add data rows
    foreach ($leads as $lead) {
        // Get assigned user name
        $assignedTo = '-';
        if ($lead['assigned_to']) {
            $uStmt = $db->prepare("SELECT full_name FROM users WHERE id = ?");
            $uStmt->execute([$lead['assigned_to']]);
            $user = $uStmt->fetch();
            if ($user) $assignedTo = $user['full_name'];
        }

        fputcsv($output, [
            $lead['lead_id'],
            $lead['lead_date'],
            $lead['company_client_name'],
            $lead['contact_person'],
            $lead['mobile_number'],
            $lead['alternative_number'],
            $lead['email_id'],
            $lead['city'],
            $lead['state'],
            $lead['source_of_lead'],
            $lead['service_interested_in'],
            $lead['lead_category'],
            $lead['lead_status'],
            $lead['priority'],
            $assignedTo,
            $lead['next_followup_date'],
            $lead['last_followup_notes'],
            $lead['requirement_details'],
            $lead['estimated_budget'],
            $lead['proposal_sent'] ? 'Yes' : 'No',
            $lead['meeting_scheduled'] ? 'Yes' : 'No',
            $lead['quotation_sent'] ? 'Yes' : 'No',
            $lead['deal_status'],
            $lead['expected_closing_date'],
            $lead['payment_status'],
            $lead['client_onboard_date'],
            $lead['project_start_date'],
            $lead['project_status'],
            $lead['reference_by'],
            $lead['website_social_link'],
            $lead['remarks_notes']
        ]);
    }

    fclose($output);
    exit;

} catch (Exception $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}