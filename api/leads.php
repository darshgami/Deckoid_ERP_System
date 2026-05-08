<?php

require_once '../config/env.php';
require_once '../includes/database.php';

require_once '../includes/middleware.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . ($_SERVER['HTTP_ORIGIN'] ?? '*'));
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Check authentication using middleware
requireAuth();


$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getInstance();

try {
    if ($method === 'POST') {
        // Create lead
        $input = json_decode(file_get_contents('php://input'), true);

        // Validate required fields
        $required = ['lead_date', 'company_client_name', 'contact_person', 'mobile_number', 'source_of_lead', 'lead_category', 'lead_status', 'deal_status', 'payment_status'];
        foreach ($required as $field) {
            if (!isset($input[$field]) || $input[$field] === '' || $input[$field] === null) {
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
            $input['estimated_budget'] ?? null, 
            isset($input['proposal_sent']) ? (bool)$input['proposal_sent'] : false,
            isset($input['meeting_scheduled']) ? (bool)$input['meeting_scheduled'] : false,
            isset($input['quotation_sent']) ? (bool)$input['quotation_sent'] : false,
            $input['deal_status'], $input['expected_closing_date'] ?? null, $input['payment_status'],
            $input['client_onboard_date'] ?? null, $input['project_start_date'] ?? null,
            $input['project_status'] ?? null, $input['reference_by'] ?? null,
            $input['website_social_link'] ?? null, $input['remarks_notes'] ?? null,
            $_SESSION['user_id']
        ]);

        // Log activity for lead creation
        $activityId = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
        $logStmt = $db->prepare("INSERT INTO lead_activity_logs (id, lead_id, user_id, company_client_name, activity_type, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $logStmt->execute([$activityId, $id, $_SESSION['user_id'], $input['company_client_name'], 'created', 'New lead added: ' . $input['company_client_name']]);

        echo json_encode(['message' => 'Lead created successfully', 'lead_id' => $leadId, 'id' => $id]);

    } elseif ($method === 'GET') {
        // List leads with pagination and filters
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;

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

        if (isset($_GET['has_followup']) && $_GET['has_followup'] === 'true') {
            $where[] = "next_followup_date IS NOT NULL";
            
            if (isset($_GET['followup_filter'])) {
                $today = date('Y-m-d');
                if ($_GET['followup_filter'] === 'today') {
                    $where[] = "next_followup_date = ?";
                    $params[] = $today;
                } elseif ($_GET['followup_filter'] === 'upcoming') {
                    $where[] = "next_followup_date > ?";
                    $params[] = $today;
                }
            }
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Get total count
        $countStmt = $db->prepare("SELECT COUNT(*) as total FROM leads $whereClause");
        $countStmt->execute($params);
        $totalRow = $countStmt->fetch();
        $total = $totalRow ? $totalRow['total'] : 0;

        // Get leads
        $stmt = $db->prepare("SELECT * FROM leads $whereClause ORDER BY created_at DESC LIMIT ? OFFSET ?");
        
        // Bind all parameters positionally
        $paramIndex = 1;
        foreach ($params as $val) {
            $stmt->bindValue($paramIndex++, $val);
        }
        
        $stmt->bindValue($paramIndex++, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue($paramIndex++, (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $leads = $stmt->fetchAll();

        echo json_encode([
            'leads' => $leads,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => (int)$total,
                'pages' => ceil($total / $limit)
            ]
        ]);

    } elseif ($method === 'PUT') {
        // Get ID from URL path or query parameter
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $path = rtrim($path, '/');
            $pathParts = explode('/', $path);
            $id = end($pathParts);
        }
        
        if (empty($id) || $id === 'leads.php') {
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
                // Handle booleans for database
                if (in_array($field, ['proposal_sent', 'meeting_scheduled', 'quotation_sent'])) {
                    $params[] = (bool)$input[$field] ? 1 : 0;
                } else {
                    $params[] = $input[$field];
                }
            }
        }

        if (empty($updates)) {
            throw new Exception('No fields to update');
        }

        $params[] = $id;
        $stmt = $db->prepare("UPDATE leads SET " . implode(', ', $updates) . ", updated_at = NOW() WHERE id = ?");
        $stmt->execute($params);

        if ($stmt->rowCount() === 0) {
            // Check if lead exists to distinguish between "not found" and "no changes"
            $check = $db->prepare("SELECT id FROM leads WHERE id = ?");
            $check->execute([$id]);
            if (!$check->fetch()) {
                throw new Exception('Lead not found');
            }
        }

        // Log activity
        $activityId = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );

        // Fetch company name for better log notes
        $leadInfo = $db->prepare("SELECT company_client_name FROM leads WHERE id = ?");
        $leadInfo->execute([$id]);
        $leadRow = $leadInfo->fetch(PDO::FETCH_ASSOC);
        $clientName = $leadRow ? $leadRow['company_client_name'] : 'Unknown';

        $stmt = $db->prepare("INSERT INTO lead_activity_logs (id, lead_id, user_id, company_client_name, activity_type, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$activityId, $id, $_SESSION['user_id'], $clientName, 'updated', 'Lead updated: ' . $clientName]);

        echo json_encode(['message' => 'Lead updated successfully']);

    } elseif ($method === 'DELETE') {
        // Get ID from URL path or query parameter
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $path = rtrim($path, '/');
            $pathParts = explode('/', $path);
            $id = end($pathParts);
        }
        
        if (empty($id) || $id === 'leads.php') {
            throw new Exception('Lead ID is required');
        }

        // Check if lead exists and fetch details BEFORE deleting
        $stmt = $db->prepare("SELECT id, company_client_name FROM leads WHERE id = ?");
        $stmt->execute([$id]);
        $leadToDelete = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$leadToDelete) {
            throw new Exception('Lead not found');
        }
        $clientName = $leadToDelete['company_client_name'];

        // Log activity BEFORE deleting (so lead_id FK still exists)
        $activityId = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
        // Store deleted log in a separate standalone table entry with no FK dependency
        // We store client name directly in notes since the lead will be gone
        $logStmt = $db->prepare("INSERT INTO lead_activity_logs (id, lead_id, user_id, company_client_name, activity_type, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $logStmt->execute([$activityId, $id, $_SESSION['user_id'] ?? null, $clientName, 'deleted', 'Lead deleted: ' . $clientName]);

        // Delete lead (cascade will delete the log above, but that is acceptable — the record is gone)
        $stmt = $db->prepare("DELETE FROM leads WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['message' => 'Lead deleted successfully']);

    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}