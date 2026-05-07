<?php

require_once '../config/env.php';
require_once '../includes/database.php';

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

    // Get all leads
    $stmt = $db->query("SELECT * FROM leads ORDER BY created_at DESC");
    $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($leads)) {
        http_response_code(404);
        echo json_encode(['error' => 'No leads found to export']);
        exit;
    }

    // Generate CSV content
    $headers = [
        'Lead ID', 'Lead Date', 'Company/Client Name', 'Contact Person', 'Mobile Number',
        'Alternative Number', 'Email ID', 'City', 'State', 'Source of Lead',
        'Service Interested In', 'Lead Category', 'Lead Status', 'Priority',
        'Next Followup Date', 'Last Followup Notes', 'Requirement Details',
        'Estimated Budget', 'Proposal Sent', 'Meeting Scheduled', 'Quotation Sent',
        'Deal Status', 'Expected Closing Date', 'Payment Status', 'Client Onboard Date',
        'Project Start Date', 'Project Status', 'Reference By', 'Website/Social Link',
        'Remarks/Notes', 'Created At'
    ];

    $csvContent = implode(',', array_map(function($header) {
        return '"' . str_replace('"', '""', $header) . '"';
    }, $headers)) . "\n";

    foreach ($leads as $lead) {
        $data = [
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
            $lead['remarks_notes'],
            $lead['created_at']
        ];

        $csvContent .= implode(',', array_map(function($value) {
            return '"' . str_replace('"', '""', $value ?? '') . '"';
        }, $data)) . "\n";
    }

    // Generate filename
    $filename = 'leads_export_' . date('Y-m-d_H-i-s') . '.csv';
    $filepath = Env::get('EXPORT_PATH', '/exports') . '/' . $filename;

    // Ensure export directory exists
    $exportDir = __DIR__ . '/../exports';
    if (!is_dir($exportDir)) {
        mkdir($exportDir, 0755, true);
    }

    $fullPath = $exportDir . '/' . $filename;

    // Save CSV file
    file_put_contents($fullPath, $csvContent);

    // Return file URL
    $fileUrl = str_replace($_SERVER['DOCUMENT_ROOT'], '', $fullPath);

    echo json_encode([
        'message' => 'Export completed successfully',
        'file_url' => $fileUrl,
        'filename' => $filename
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}