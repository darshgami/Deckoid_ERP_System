<?php

require_once '../config/env.php';
require_once '../includes/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Check authentication (simplified for now)
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';
if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getInstance();

try {
    if ($method === 'POST') {
        // Create lead
        $input = json_decode(file_get_contents('php://input'), true);

        // Validate required fields
        $required = ['lead_date', 'company_client_name', 'contact_person', 'mobile_number', 'source_of_lead', 'lead_category', 'lead_status', 'deal_status', 'payment_status'];
        foreach ($required as $field) {
            if (!isset($input[$field]) || empty($input[$field])) {
                throw new Exception("Field '{$field}' is required");
            }
        }

        // Generate lead_id
        $leadId = 'L' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Check for duplicate mobile
        $stmt = $db->prepare("SELECT id FROM leads WHERE mobile_number = ?");
        $stmt->execute([$input['mobile_number']]);
        if ($stmt->fetch()) {
            throw new Exception('Lead with this mobile number already exists');
        }

        // Insert lead
        $id = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );

        $stmt = $db->prepare("INSERT INTO leads (
            id, lead_id, lead_date, company_client_name, contact_person, mobile_number,
            alternative_number, email_id, city, state, source_of_lead, service_interested_in,
            lead_category, lead_status, priority, next_followup_date, last_followup_notes,
            requirement_details, estimated_budget, proposal_sent, meeting_scheduled,
            quotation_sent, deal_status, expected_closing_date, payment_status,
            client_onboard_date, project_start_date, project_status, reference_by,
            website_social_link, remarks_notes, created_by
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $id, $leadId, $input['lead_date'], $input['company_client_name'], $input['contact_person'],
            $input['mobile_number'], $input['alternative_number'] ?? null, $input['email_id'] ?? null,
            $input['city'] ?? null, $input['state'] ?? null, $input['source_of_lead'],
            $input['service_interested_in'] ?? null, $input['lead_category'], $input['lead_status'],
            $input['priority'] ?? 'Medium', $input['next_followup_date'] ?? null,
            $input['last_followup_notes'] ?? null, $input['requirement_details'] ?? null,
            $input['estimated_budget'] ?? null, $input['proposal_sent'] ?? false,
            $input['meeting_scheduled'] ?? false, $input['quotation_sent'] ?? false,
            $input['deal_status'], $input['expected_closing_date'] ?? null, $input['payment_status'],
            $input['client_onboard_date'] ?? null, $input['project_start_date'] ?? null,
            $input['project_status'] ?? null, $input['reference_by'] ?? null,
            $input['website_social_link'] ?? null, $input['remarks_notes'] ?? null,
            'user-id-placeholder' // TODO: Get from JWT
        ]);

        echo json_encode(['message' => 'Lead created successfully', 'lead_id' => $leadId]);

    } elseif ($method === 'GET') {
        // List leads with pagination and filters
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;

        $where = [];
        $params = [];

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = '%' . $_GET['search'] . '%';
            $where[] = "(company_client_name LIKE ? OR contact_person LIKE ? OR mobile_number LIKE ? OR email_id LIKE ?)";
            $params = array_merge($params, [$search, $search, $search, $search]);
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

        // Get total count
        $countStmt = $db->prepare("SELECT COUNT(*) as total FROM leads $whereClause");
        $countStmt->execute($params);
        $total = $countStmt->fetch()['total'];

        // Get leads
        $stmt = $db->prepare("SELECT * FROM leads $whereClause ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute(array_merge($params, [$limit, $offset]));
        $leads = $stmt->fetchAll();

        echo json_encode([
            'leads' => $leads,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);

    } elseif ($method === 'PUT') {
        // Update lead
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $pathParts = explode('/', $path);
        $leadId = end($pathParts);

        if (empty($leadId)) {
            throw new Exception('Lead ID is required');
        }

        $input = json_decode(file_get_contents('php://input'), true);

        // Build update query
        $updates = [];
        $params = [];

        $allowedFields = [
            'lead_date', 'company_client_name', 'contact_person', 'mobile_number',
            'alternative_number', 'email_id', 'city', 'state', 'source_of_lead',
            'service_interested_in', 'lead_category', 'lead_status', 'priority',
            'assigned_to', 'next_followup_date', 'last_followup_notes',
            'requirement_details', 'estimated_budget', 'proposal_sent',
            'meeting_scheduled', 'quotation_sent', 'deal_status',
            'expected_closing_date', 'payment_status', 'client_onboard_date',
            'project_start_date', 'project_status', 'reference_by',
            'website_social_link', 'remarks_notes'
        ];

        foreach ($allowedFields as $field) {
            if (isset($input[$field])) {
                $updates[] = "$field = ?";
                $params[] = $input[$field];
            }
        }

        if (empty($updates)) {
            throw new Exception('No fields to update');
        }

        $params[] = $leadId;
        $stmt = $db->prepare("UPDATE leads SET " . implode(', ', $updates) . ", updated_at = NOW() WHERE id = ?");
        $stmt->execute($params);

        // Log activity
        $activityId = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );

        $stmt = $db->prepare("INSERT INTO lead_activity_logs (id, lead_id, user_id, activity_type, notes) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$activityId, $leadId, 'user-id-placeholder', 'updated', 'Lead details updated']);

        echo json_encode(['message' => 'Lead updated successfully']);

    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}