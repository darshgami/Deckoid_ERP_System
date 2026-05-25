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
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Allow both staff and admin
requireAuth();

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

    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    // Get filtered leads for Quick List
    $stmt = $db->prepare("SELECT lead_id, company_client_name, mobile_number, remarks_notes, last_followup_notes, next_followup_date FROM leads $whereClause ORDER BY created_at DESC");
    $stmt->execute($params);
    $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($leads)) {
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['error' => 'No leads found to export']);
        exit;
    }

    // Set headers for CSV download
    $filename = 'quick_leads_export_' . date('Y-m-d_H-i-s') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);

    // Create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');

    // Add UTF-8 BOM for Excel compatibility (crucial for Gujarati text)
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Add headers exactly as requested
    fputcsv($output, [
        'Lead ID', 'Name', 'Number', 'Remarks', 'Last Follow-up Notes', 'Next Follow-up Date'
    ]);

    // Add data rows
    foreach ($leads as $lead) {
        $nextFollowupDate = $lead['next_followup_date'] ? formatDate($lead['next_followup_date']) : '-';
        $lastFollowupNotes = $lead['last_followup_notes'] ? $lead['last_followup_notes'] : '-';
        $remarks = $lead['remarks_notes'] ? $lead['remarks_notes'] : '-';
        $name = $lead['company_client_name'] ? $lead['company_client_name'] : '-';
        $number = $lead['mobile_number'] ? $lead['mobile_number'] : '-';

        // Escaping line breaks in notes/remarks to ensure CSV integrity
        $lastFollowupNotes = str_replace(array("\r", "\n"), " ", $lastFollowupNotes);
        $remarks = str_replace(array("\r", "\n"), " ", $remarks);

        fputcsv($output, [
            $lead['lead_id'],
            $name,
            $number,
            $remarks,
            $lastFollowupNotes,
            $nextFollowupDate
        ]);
    }

    fclose($output);
    exit;

} catch (Exception $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
