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
            if ($value === '' || (is_string($value) && trim($value) === '') || $value === 'null') {
                $input[$key] = null;
            }
        }
        
        // Ensure assigned_to is properly null if not a valid UUID format
        if (isset($input['assigned_to']) && strlen($input['assigned_to']) < 32) {
            $input['assigned_to'] = null;
        }

        // Professional Validation
        $validator = new Validator();
        $rules = [
            'lead_date' => 'required',
            'company' => 'required|min:3',
            'contact_person' => 'required|min:3',
            'mobile_number' => 'required|mobile',
            'lead_category' => 'required',
            'lead_status' => 'required'
        ];

        if (!isset($input['payment_status'])) {
            $input['payment_status'] = 'Pending';
        }

        if (!$validator->validate($input, $rules)) {
            throw new Exception($validator->getFirstError());
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



            // Insert lead
            $id = generateUUID();

            try {
                $stmt = $db->prepare("INSERT INTO leads (
                    id, lead_id, lead_date, company, contact_person, mobile_number,
                    email_id, city, state, lead_category, lead_status, assigned_to, next_followup_date,
                    estimated_budget, payment_status, reference_by, remarks, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $stmt->execute([
                    $id, $leadId, $input['lead_date'], $input['company'], $input['contact_person'],
                    $input['mobile_number'], $input['email_id'] ?? null,
                    $input['city'] ?? null, $input['state'] ?? null,
                    $input['lead_category'], $input['lead_status'],
                    $input['assigned_to'] ?? null, $input['next_followup_date'] ?? null,
                    $input['estimated_budget'] ?? null, $input['payment_status'],
                    $input['reference_by'] ?? null, $input['remarks'] ?? null,
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
        $logStmt = $db->prepare("INSERT INTO lead_activity_logs (id, lead_id, user_id, company, activity_type, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $logStmt->execute([$activityId, $id, $_SESSION['user_id'], $input['company'], 'created', 'New lead added: ' . $input['company']]);

        // Auto-create followup record if date is provided
        if (!empty($input['next_followup_date'])) {
            $fId = generateUUID();
            $fStmt = $db->prepare("INSERT INTO followups (id, lead_id, followup_date, created_by) VALUES (?, ?, ?, ?)");
            $fStmt->execute([$fId, $id, $input['next_followup_date'], $_SESSION['user_id']]);
        }

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

        if (AuthController::getCurrentRole() === 'staff') {
            $where[] = "l.assigned_to = ?";
            $params[] = $_SESSION['user_id'];
        }

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = '%' . $_GET['search'] . '%';
            $where[] = "(l.company LIKE ? OR l.contact_person LIKE ? OR l.mobile_number LIKE ? OR l.email_id LIKE ? OR l.lead_id LIKE ?)";
            $params = array_merge($params, [$search, $search, $search, $search, $search]);
        }

        if (isset($_GET['category']) && !empty($_GET['category'])) {
            $where[] = "l.lead_category = ?";
            $params[] = $_GET['category'];
        }

        if (isset($_GET['lead_status']) && !empty($_GET['lead_status'])) {
            $where[] = "l.lead_status = ?";
            $params[] = $_GET['lead_status'];
        }

        if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
            $where[] = "l.lead_date >= ?";
            $params[] = $_GET['date_from'];
        }

        if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
            $where[] = "l.lead_date <= ?";
            $params[] = $_GET['date_to'];
        }

        if (isset($_GET['has_followup']) && $_GET['has_followup'] === 'true') {
            $where[] = "l.next_followup_date IS NOT NULL";
            $where[] = "l.lead_status != 'Convert'";
            
            if (isset($_GET['followup_filter'])) {
                $today = date('Y-m-d');
                if ($_GET['followup_filter'] === 'today') {
                    $where[] = "l.next_followup_date = ?";
                    $params[] = $today;
                } elseif ($_GET['followup_filter'] === 'upcoming') {
                    $where[] = "l.next_followup_date > ?";
                    $params[] = $today;
                }
            }
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Get total count
        $countStmt = $db->prepare("SELECT COUNT(*) as total FROM leads l $whereClause");
        $countStmt->execute($params);
        $totalRow = $countStmt->fetch();
        $total = $totalRow ? $totalRow['total'] : 0;

        // Get leads with assigned user names
        $stmt = $db->prepare("SELECT l.*, u.full_name as assigned_to_name, c.full_name as created_by_name 
                              FROM leads l 
                              LEFT JOIN users u ON l.assigned_to = u.id 
                              LEFT JOIN users c ON l.created_by = c.id 
                              $whereClause 
                              ORDER BY l.lead_date DESC, l.created_at DESC 
                              LIMIT ? OFFSET ?");
        
        // Bind all parameters positionally
        $paramIndex = 1;
        foreach ($params as $val) {
            $stmt->bindValue($paramIndex++, $val);
        }
        
        $stmt->bindValue($paramIndex++, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue($paramIndex++, (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ensure remarks is never null
        foreach ($leads as &$lead) {
            if ($lead['remarks'] === null) {
                $lead['remarks'] = '';
            }
        }
        unset($lead);

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
            throw new Exception('Lead ID is required for updating.');
        }

        $input = json_decode(file_get_contents('php://input'), true);

        $leadRecord = $db->prepare("SELECT assigned_to FROM leads WHERE id = ?");
        $leadRecord->execute([$id]);
        $leadRecordRow = $leadRecord->fetch(PDO::FETCH_ASSOC);

        if (!$leadRecordRow) {
            throw new Exception('The requested lead could not be found.');
        }

        if (AuthController::getCurrentRole() === 'staff' && ($leadRecordRow['assigned_to'] ?? null) !== $_SESSION['user_id']) {
            ApiResponse::send(ApiResponse::error('Access denied. You can only work on leads assigned to you.'), 403);
        }

        // Normalize empty strings to null to prevent database errors for DATE/DECIMAL columns
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                if ($value === '' || (is_string($value) && trim($value) === '') || $value === 'null') {
                    $input[$key] = null;
                }
            }
            
            // Ensure assigned_to is properly null if not a valid UUID format
            if (array_key_exists('assigned_to', $input) && $input['assigned_to'] !== null && strlen($input['assigned_to']) < 32) {
                $input['assigned_to'] = null;
            }
        }

        // Build update query
        $updates = [];
        $params = [];

        $allowedFields = [
            'lead_date', 'company', 'contact_person', 'mobile_number', 'email_id',
            'city', 'state', 'lead_category', 'lead_status',
            'assigned_to', 'next_followup_date', 'estimated_budget', 
            'payment_status', 'reference_by', 'remarks'
        ];

        // Validation for updated fields
        $validator = new Validator();
        $rules = [];
        if (isset($input['mobile_number'])) $rules['mobile_number'] = 'mobile';
        if (isset($input['email_id'])) $rules['email_id'] = 'email_id';
        if (isset($input['company'])) $rules['company'] = 'min:3';
        
        if (!empty($rules) && !$validator->validate($input, $rules)) {
            throw new Exception($validator->getFirstError());
        }

        // Check for mobile duplicate if mobile is being updated
        if (isset($input['mobile_number'])) {
            $stmt = $db->prepare("SELECT id FROM leads WHERE mobile_number = ? AND id != ?");
            $stmt->execute([$input['mobile_number'], $id]);
            if ($stmt->fetch()) {
                throw new Exception('A lead with this mobile number already exists');
            }
        }

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $input)) {
                $updates[] = "$field = ?";
                $params[] = $input[$field];
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
        $leadInfo = $db->prepare("SELECT company FROM leads WHERE id = ?");
        $leadInfo->execute([$id]);
        $leadRow = $leadInfo->fetch(PDO::FETCH_ASSOC);
        $clientName = $leadRow ? $leadRow['company'] : 'Unknown';
        
        $addWork = isset($input['add_work']) ? $input['add_work'] : 'General Service';

        $stmt = $db->prepare("INSERT INTO lead_activity_logs (id, lead_id, user_id, company, activity_type, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$activityId, $id, $_SESSION['user_id'], $clientName, 'updated', 'Lead updated: ' . $clientName]);

        // Sync followup record
        if (isset($input['next_followup_date']) && !empty($input['next_followup_date'])) {
            // Delete existing active followups to recreate a fresh one for this date
            $delStmt = $db->prepare("DELETE FROM followups WHERE lead_id = ? AND status = 'Active'");
            $delStmt->execute([$id]);

            $fId = generateUUID();
            $fStmt = $db->prepare("INSERT INTO followups (id, lead_id, followup_date, created_by) VALUES (?, ?, ?, ?)");
            $fStmt->execute([$fId, $id, $input['next_followup_date'], $_SESSION['user_id']]);
        }

        // Auto-create onboarding record if converted
        if (isset($input['lead_status']) && $input['lead_status'] === 'Convert') {
            $chk = $db->prepare("SELECT id FROM customer_onboarding WHERE lead_id = ?");
            $chk->execute([$id]);
            if (!$chk->fetch()) {
                $obId = generateUUID();
                $onboardingDate = !empty($input['onboarding_date']) ? $input['onboarding_date'] : date('Y-m-d');
                $obCompany = $input['company'] ?? $clientName;
                $obStmt = $db->prepare("INSERT INTO customer_onboarding (id, lead_id, project_name, company, add_work, onboarding_date, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $obStmt->execute([$obId, $id, $clientName, $obCompany, $addWork, $onboardingDate, $_SESSION['user_id']]);
            }
            // Delete followup record upon conversion
            $delFollowup = $db->prepare("DELETE FROM followups WHERE lead_id = ?");
            $delFollowup->execute([$id]);
        }

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
        $stmt = $db->prepare("SELECT id, company FROM leads WHERE id = ?");
        $stmt->execute([$id]);
        $leadToDelete = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$leadToDelete) {
            throw new Exception('The requested lead could not be found.');
        }
        $clientName = $leadToDelete['company'];

        // Log activity BEFORE deleting
        $activityId = generateUUID();
        $logStmt = $db->prepare("INSERT INTO lead_activity_logs (id, lead_id, user_id, company, activity_type, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $logStmt->execute([$activityId, $id, $_SESSION['user_id'] ?? null, $clientName, 'deleted', 'Lead deleted: ' . $clientName]);

        // Delete lead
        $stmt = $db->prepare("DELETE FROM leads WHERE id = ?");
        $stmt->execute([$id]);

        ApiResponse::send(ApiResponse::success('Lead deleted successfully'));

    } else {
        ApiResponse::send(ApiResponse::error('Method not allowed'), 405);
    }

} catch (Throwable $e) {
    Logger::error('Leads API Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    
    $message = $e->getMessage();
    
    // Always return a user-friendly message for validation errors without 400 HTTP lead_status
    // to prevent browser console from throwing '400 Bad Request'
    $isValidation = (
        strpos($message, 'required') !== false || 
        strpos($message, 'exists') !== false || 
        strpos($message, 'found') !== false || 
        strpos($message, 'valid') !== false ||
        strpos($message, 'Integrity constraint violation') !== false
    );
    
    $userFriendlyMessage = $isValidation ? $message : 'An unexpected error occurred. Please try again later.';
    
    ApiResponse::send(ApiResponse::error($userFriendlyMessage));
}
