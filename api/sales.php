<?php
header('Content-Type: application/json');
require_once '../includes/middleware.php';
require_once '../includes/database.php';
require_once '../includes/utils.php';

$db = Database::getInstance();
$method = $_SERVER['REQUEST_METHOD'];

// Ensure user is logged in
requireAuth();

if ($method === 'GET') {
    if (isset($_GET['check_number'])) {
        $number = $_GET['check_number'];
        $stmt = $db->prepare("SELECT id FROM invoices WHERE invoice_number = ?");
        $stmt->execute([$number]);
        echo json_encode(['exists' => $stmt->fetch() ? true : false]);
        exit;
    }

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;
    
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $type = isset($_GET['type']) ? $_GET['type'] : '';
    $dateFrom = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : '';
    $dateTo = isset($_GET['dateTo']) ? $_GET['dateTo'] : '';

    $where = ["1=1"];
    $params = [];

    if ($search) {
        $where[] = "(party_name LIKE ? OR invoice_number LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if ($type) {
        $where[] = "invoice_type = ?";
        $params[] = $type;
    }

    if ($dateFrom) {
        $where[] = "invoice_date >= ?";
        $params[] = $dateFrom;
    }

    if ($dateTo) {
        $where[] = "invoice_date <= ?";
        $params[] = $dateTo;
    }

    $whereClause = implode(" AND ", $where);

    // Get total count
    $countStmt = $db->prepare("SELECT COUNT(*) FROM invoices WHERE $whereClause");
    $countStmt->execute($params);
    $total = $countStmt->fetchColumn();

    // Get data
    $stmt = $db->prepare("SELECT * FROM invoices WHERE $whereClause ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
    $stmt->execute($params);
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'invoices' => $invoices,
            'pagination' => [
                'total' => (int)$total,
                'page' => $page,
                'pages' => ceil($total / $limit),
                'limit' => $limit
            ]
        ]
    ]);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        ApiResponse::send(ApiResponse::error('Invalid request data'), 400);
    }

    // Server-side Validation
    $validator = new Validator();
    $rules = [
        'invoice_number' => 'required|min:3',
        'invoice_date' => 'required',
        'party_name' => 'required|min:3',
        'mobile_number' => 'required|mobile',
        'address' => 'required',
        'sub_total' => 'required|numeric',
        'grand_total' => 'required|numeric'
    ];

    // Conditional GST Validation
    if ($data['invoice_type'] === 'With GST') {
        $rules['gstin'] = 'required|gstin';
        $rules['place_of_supply'] = 'required';
    }

    if (!$validator->validate($data, $rules)) {
        ApiResponse::send(ApiResponse::error($validator->getFirstError()), 400);
    }

    // Numeric consistency check (Prevent negative or zero totals)
    if ($data['grand_total'] <= 0) {
        ApiResponse::send(ApiResponse::error('Grand total must be greater than zero'), 400);
    }

    // Duplicate Invoice Number Check (Security & Data Integrity)
    $stmt = $db->prepare("SELECT id FROM invoices WHERE invoice_number = ?");
    $stmt->execute([$data['invoice_number']]);
    if ($stmt->fetch()) {
        ApiResponse::send(ApiResponse::error("Invoice number '" . h($data['invoice_number']) . "' already exists"), 400);
    }

    try {
        $db->beginTransaction();

        $invoice_id = generateUUID();
        
        // Insert into invoices
        $stmt = $db->prepare("INSERT INTO invoices (id, invoice_number, invoice_date, invoice_type, party_name, address, mobile_number, gstin, place_of_supply, sub_total, gst_total, grand_total, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $invoice_id,
            trim($data['invoice_number']),
            $data['invoice_date'],
            $data['invoice_type'],
            trim($data['party_name']),
            trim($data['address']),
            trim($data['mobile_number']),
            $data['gstin'] ?? null,
            $data['place_of_supply'] ?? null,
            $data['sub_total'],
            $data['gst_total'],
            $data['grand_total'],
            $_SESSION['user_id']
        ]);

        // Insert items
        $itemStmt = $db->prepare("INSERT INTO invoice_items (id, invoice_id, service_name, hsn_sac, qty, rate, amount) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        for ($i = 0; $i < count($data['service_name']); $i++) {
            $service = $data['service_name'][$i];
            if ($service === 'Other' && !empty($data['custom_service'][$i])) {
                $service = $data['custom_service'][$i];
            }

            if (empty($service)) continue;

            $itemStmt->execute([
                generateUUID(),
                $invoice_id,
                $service,
                $data['hsn_sac'][$i],
                $data['qty'][$i],
                $data['rate'][$i],
                $data['amount'][$i]
            ]);
        }

        $db->commit();
        echo json_encode(['success' => true, 'message' => 'Invoice saved successfully', 'id' => $invoice_id]);
    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

if ($method === 'DELETE') {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Missing ID']);
        exit;
    }

    try {
        $stmt = $db->prepare("DELETE FROM invoices WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Invoice deleted']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
