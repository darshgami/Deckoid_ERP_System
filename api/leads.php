<?php

require_once '../config/env.php';
require_once '../includes/database.php';

require_once '../includes/middleware.php';
require_once '../includes/utils.php';

header('Content-Type: application/json');
apply_api_cors_headers('GET, POST, PUT, OPTIONS');

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

        // Normalize empty strings to null to prevent database errors for DATE/DECIMAL columns
        foreach ($input as $key => $value) {
            if ($value === '') {
                $input[$key] = null;
            }
        }

        // Validate required fields
        $required = ['lead_date', 'company_client_name', 'contact_person', 'mobile_number', 'source_of_lead', 'lead_category', 'lead_status', 'deal_status', 'payment_status'];
        foreach ($required as $field) {
            if (!isset($input[$field]) || $input[$field] === '' || $input[$field] === null) {
                throw new Exception("The field '{$field}' is required.");
            }
        }

        // Generate Lead ID DK0001 format with retry logic for race conditions
        $maxRetries = 5;
        $attempt = 0;
        $id = null;
        $leadId = null;

        while ($attempt < $maxRetries) {
            $attempt++;
            
            $lastIdStmt = $db->query("SELECT lead_id FROM leads WHERE lead_id LIKE 'DK%' ORDER BY lead_id DESC LIMIT 1");
            $lastLead = $lastIdStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($lastLead) {
                $lastNum = (int)substr($lastLead['lead_id'], 2);
                $newNum = $lastNum + 1;
            } else {
                $newNum = 1;
            }
            $leadId = 'DK' . str_pad($newNum, 4, '0', STR_PAD_LEFT);

            // Check for duplicate mobile (only on first attempt)
            if ($attempt === 1) {
                $stmt = $db->prepare("SELECT id FROM leads WHERE mobile_number = ?");
                $stmt->execute([$input['mobile_number']]);
                if ($stmt->fetch()) {
                    throw new Exception('A lead with this mobile number already exists');
                }
            }

            // Insert lead
            $id = generateUUID();

            try {
                $stmt = $db->prepare("INSERT INTO leads (
                    id, lead_id, lead_date, company_client_name, contact_person, mobile_number,
                    alternative_number, email_id, city, state, source_of_lead, service_interested_in,
                    lead_category, lead_status, priority, assigned_to, next_followup_date, last_followup_notes,
                    requirement_details, estimated_budget, proposal_sent, meeting_scheduled,
                    quotation_sent, deal_status, expected_closing_date, payment_status,
                    client_onboard_date, project_start_date, project_status, reference_by,
                    website_social_link, remarks_notes, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $stmt->execute([
                    $id, $leadId, $input['lead_date'], $input['company_client_name'], $input['contact_person'],
                    $input['mobile_number'], $input['alternative_number'] ?? null, $input['email_id'] ?? null,
                    $input['city'] ?? null, $input['state'] ?? null, $input['source_of_lead'],
                    $input['service_interested_in'] ?? null, $input['lead_category'], $input['lead_status'],
                    $input['priority'] ?? 'Medium', $input['assigned_to'] ?? null, $input['next_followup_date'] ?? null,
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
                
                break;
            } catch (PDOException $e) {
                if ($e->getCode() == 23000 && strpos($e->getMessage(), 'lead_id') !== false) {
                    if ($attempt >= $maxRetries) {
                        Logger::error('Lead ID generation failed after retries');
                        throw new Exception('Unable to generate a unique lead identifier. Please try again.');
                    }
                    continue;
                } else {
                    throw $e;
                }
            }
        }

        // Log activity for lead creation
        $activityId = generateUUID();
        $logStmt = $db->prepare("INSERT INTO lead_activity_logs (id, lead_id, user_id, company_client_name, activity_type, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $logStmt->execute([$activityId, $id, $_SESSION['user_id'], $input['company_client_name'], 'created', 'New lead added: ' . $input['company_client_name']]);

        ApiResponse::send(ApiResponse::success('Lead created successfully', [
            'lead_id' => $leadId,
            'id' => $id
        ]));

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

        if (isset($_GET['service']) && !empty($_GET['service'])) {
            $where[] = "service_interested_in = ?";
            $params[] = $_GET['service'];
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

        // Get leads with assigned user names
        $stmt = $db->prepare("SELECT l.*, u.full_name as assigned_to_name, c.full_name as created_by_name 
                             FROM leads l 
                             LEFT JOIN users u ON l.assigned_to = u.id 
                             LEFT JOIN users c ON l.created_by = c.id 
                             $whereClause 
                             ORDER BY l.created_at DESC LIMIT ? OFFSET ?");
        
        // Bind all parameters positionally
        $paramIndex = 1;
        foreach ($params as $val) {
            $stmt->bindValue($paramIndex++, $val);
        }
        
        $stmt->bindValue($paramIndex++, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue($paramIndex++, (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ApiResponse::send(ApiResponse::success('Leads retrieved successfully', [
            'leads' => $leads,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => (int)$total,
                'pages' => ceil($total / $limit)
            ]
        ]));

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
            throw new Exception('A valid Lead ID is required for updates.');
        }

        $input = json_decode(file_get_contents('php://input'), true);

        // Normalize empty strings to null to prevent database errors for DATE/DECIMAL columns
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                if ($value === '') {
                    $input[$key] = null;
                }
            }
        }

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
            if (array_key_exists($field, $input)) {
                $updates[] = "$field = ?";
                // Handle booleans for database
                if (in_array($field, ['proposal_sent', 'meeting_scheduled', 'quotation_sent'])) {
                    $params[] = ($input[$field] === '1' || $input[$field] === 1 || $input[$field] === true) ? 1 : 0;
                } else {
                    $params[] = $input[$field];
                }
            }
        }

        if (empty($updates)) {
            throw new Exception('No valid fields were provided for update.');
        }

        $params[] = $id;
        $stmt = $db->prepare("UPDATE leads SET " . implode(', ', $updates) . ", updated_at = NOW() WHERE id = ?");
        $stmt->execute($params);

        if ($stmt->rowCount() === 0) {
            $check = $db->prepare("SELECT id FROM leads WHERE id = ?");
            $check->execute([$id]);
            if (!$check->fetch()) {
                throw new Exception('The requested lead could not be found.');
            }
        }

        // Log activity
        $activityId = generateUUID();
        
        // Fetch company name for better log notes
        $leadInfo = $db->prepare("SELECT company_client_name FROM leads WHERE id = ?");
        $leadInfo->execute([$id]);
        $leadRow = $leadInfo->fetch(PDO::FETCH_ASSOC);
        $clientName = $leadRow ? $leadRow['company_client_name'] : 'Unknown';

        $stmt = $db->prepare("INSERT INTO lead_activity_logs (id, lead_id, user_id, company_client_name, activity_type, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$activityId, $id, $_SESSION['user_id'], $clientName, 'updated', 'Lead updated: ' . $clientName]);

        ApiResponse::send(ApiResponse::success('Lead details updated successfully'));

    } elseif ($method === 'DELETE') {
        // Only admins can delete leads
        requireAdmin();

        // Get ID from URL path or query parameter
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $path = rtrim($path, '/');
            $pathParts = explode('/', $path);
            $id = end($pathParts);
        }
        
        if (empty($id) || $id === 'leads.php') {
            throw new Exception('A valid Lead ID is required for deletion.');
        }

        // Check if lead exists and fetch details BEFORE deleting
        $stmt = $db->prepare("SELECT id, company_client_name FROM leads WHERE id = ?");
        $stmt->execute([$id]);
        $leadToDelete = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$leadToDelete) {
            throw new Exception('The requested lead could not be found.');
        }
        $clientName = $leadToDelete['company_client_name'];

        // Log activity BEFORE deleting
        $activityId = generateUUID();
        $logStmt = $db->prepare("INSERT INTO lead_activity_logs (id, lead_id, user_id, company_client_name, activity_type, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $logStmt->execute([$activityId, $id, $_SESSION['user_id'] ?? null, $clientName, 'deleted', 'Lead deleted: ' . $clientName]);

        // Delete lead
        $stmt = $db->prepare("DELETE FROM leads WHERE id = ?");
        $stmt->execute([$id]);

        ApiResponse::send(ApiResponse::success('Lead deleted successfully'));

    } else {
        ApiResponse::send(ApiResponse::error('Method not allowed'), 405);
    }

} catch (Exception $e) {
    Logger::error('Leads API Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    
    $message = $e->getMessage();
    // Simple logic to keep some error messages if they are user-centric
    $isUserFriendly = (
        strpos($message, 'required') !== false || 
        strpos($message, 'exists') !== false || 
        strpos($message, 'found') !== false || 
        strpos($message, 'valid') !== false ||
        strpos($message, 'Integrity constraint violation') !== false
    );
    
    $userFriendlyMessage = $isUserFriendly ? $message : 'An unexpected error occurred. Please try again later.';
    
    ApiResponse::send(ApiResponse::error($userFriendlyMessage), 400);
}