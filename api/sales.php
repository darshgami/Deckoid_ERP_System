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
        ApiResponse::send(['exists' => $stmt->fetch() ? true : false]);
    }

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = $db->prepare("SELECT * FROM invoices WHERE id = ?");
        $stmt->execute([$id]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$invoice) {
            ApiResponse::send(ApiResponse::error('Invoice not found'), 404);
        }

        $stmt = $db->prepare("SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY created_at ASC");
        $stmt->execute([$id]);
        $invoice['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ApiResponse::send(ApiResponse::success('Invoice retrieved successfully', $invoice));
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

    ApiResponse::send(ApiResponse::success('Invoices retrieved successfully', [
        'invoices' => $invoices,
        'pagination' => [
            'total' => (int)$total,
            'page' => $page,
            'pages' => ceil($total / $limit),
            'limit' => $limit
        ]
    ]));
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

    // Secure server-side calculation for totals
    $calculated_sub_total = 0;
    if (isset($data['service_name']) && is_array($data['service_name'])) {
        for ($i = 0; $i < count($data['service_name']); $i++) {
            $service = $data['service_name'][$i];
            if ($service === 'Other' && !empty($data['custom_service'][$i])) {
                $service = $data['custom_service'][$i];
            }
            if (empty($service)) continue;
            
            $qty = isset($data['qty'][$i]) ? (float)$data['qty'][$i] : 0;
            $rate = isset($data['rate'][$i]) ? (float)$data['rate'][$i] : 0;
            $amount = round($qty * $rate, 2);
            
            // Overwrite the amount in the payload for consistency later
            $data['amount'][$i] = $amount;
            $calculated_sub_total += $amount;
        }
    }

    $calculated_gst = 0;
    if ($data['invoice_type'] === 'With GST') {
        $calculated_gst = round($calculated_sub_total * 0.18, 2);
    }

    $calculated_grand_total = round($calculated_sub_total + $calculated_gst, 2);

    // Overwrite client-provided totals with secure server calculations
    $data['sub_total'] = $calculated_sub_total;
    $data['gst_total'] = $calculated_gst;
    $data['grand_total'] = $calculated_grand_total;

    // Numeric consistency check (Prevent negative or zero totals)
    if ($data['grand_total'] <= 0) {
        ApiResponse::send(ApiResponse::error('Grand total must be greater than zero. Please add valid services.'), 400);
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
        $stmt = $db->prepare("INSERT INTO invoices (id, invoice_number, invoice_date, invoice_type, party_name, address, mobile_number, gstin, place_of_supply, sub_total, gst_total, grand_total, created_by, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
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
            $_SESSION['user_id'],
            $data['payment_status'] ?? 'Pending'
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
        ApiResponse::send(ApiResponse::success('Invoice saved successfully', ['id' => $invoice_id]));
    } catch (Throwable $e) {
        $db->rollBack();
        ApiResponse::send(ApiResponse::error($e->getMessage()), 500);
    }
}

if ($method === 'PUT') {
    $id = $_GET['id'] ?? null;
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$id || !$data) {
        ApiResponse::send(ApiResponse::error('Invalid request'), 400);
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

    if ($data['invoice_type'] === 'With GST') {
        $rules['gstin'] = 'required|gstin';
        $rules['place_of_supply'] = 'required';
    }

    if (!$validator->validate($data, $rules)) {
        ApiResponse::send(ApiResponse::error($validator->getFirstError()), 400);
    }

    // Secure server-side calculation for totals (PUT)
    $calculated_sub_total = 0;
    if (isset($data['service_name']) && is_array($data['service_name'])) {
        for ($i = 0; $i < count($data['service_name']); $i++) {
            $service = $data['service_name'][$i];
            if ($service === 'Other' && !empty($data['custom_service'][$i])) {
                $service = $data['custom_service'][$i];
            }
            if (empty($service)) continue;
            
            $qty = isset($data['qty'][$i]) ? (float)$data['qty'][$i] : 0;
            $rate = isset($data['rate'][$i]) ? (float)$data['rate'][$i] : 0;
            $amount = round($qty * $rate, 2);
            
            // Overwrite the amount in the payload for consistency later
            $data['amount'][$i] = $amount;
            $calculated_sub_total += $amount;
        }
    }

    $calculated_gst = 0;
    if ($data['invoice_type'] === 'With GST') {
        $calculated_gst = round($calculated_sub_total * 0.18, 2);
    }

    $calculated_grand_total = round($calculated_sub_total + $calculated_gst, 2);

    // Overwrite client-provided totals with secure server calculations
    $data['sub_total'] = $calculated_sub_total;
    $data['gst_total'] = $calculated_gst;
    $data['grand_total'] = $calculated_grand_total;

    // Numeric consistency check
    if ($data['grand_total'] <= 0) {
        ApiResponse::send(ApiResponse::error('Grand total must be greater than zero. Please add valid services.'), 400);
    }

    // Duplicate Check excluding current
    $stmt = $db->prepare("SELECT id FROM invoices WHERE invoice_number = ? AND id != ?");
    $stmt->execute([$data['invoice_number'], $id]);
    if ($stmt->fetch()) {
        ApiResponse::send(ApiResponse::error("Invoice number '" . h($data['invoice_number']) . "' already exists"), 400);
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("UPDATE invoices SET invoice_number = ?, invoice_date = ?, invoice_type = ?, party_name = ?, address = ?, mobile_number = ?, gstin = ?, place_of_supply = ?, sub_total = ?, gst_total = ?, grand_total = ?, payment_status = ? WHERE id = ?");
        
        $stmt->execute([
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
            $data['payment_status'] ?? 'Pending',
            $id
        ]);

        // Refresh items: Delete and Re-insert
        $db->prepare("DELETE FROM invoice_items WHERE invoice_id = ?")->execute([$id]);

        $itemStmt = $db->prepare("INSERT INTO invoice_items (id, invoice_id, service_name, hsn_sac, qty, rate, amount) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        for ($i = 0; $i < count($data['service_name']); $i++) {
            $service = $data['service_name'][$i];
            if ($service === 'Other' && !empty($data['custom_service'][$i])) {
                $service = $data['custom_service'][$i];
            }

            if (empty($service)) continue;

            $itemStmt->execute([
                generateUUID(),
                $id,
                $service,
                $data['hsn_sac'][$i],
                $data['qty'][$i],
                $data['rate'][$i],
                $data['amount'][$i]
            ]);
        }

        $db->commit();
        ApiResponse::send(ApiResponse::success('Invoice updated successfully'));
    } catch (Throwable $e) {
        $db->rollBack();
        ApiResponse::send(ApiResponse::error($e->getMessage()), 500);
    }
}

if ($method === 'DELETE') {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        ApiResponse::send(ApiResponse::error('Missing ID'), 400);
    }

    try {
        $stmt = $db->prepare("DELETE FROM invoices WHERE id = ?");
        $stmt->execute([$id]);
        ApiResponse::send(ApiResponse::success('Invoice deleted'));
    } catch (Throwable $e) {
        ApiResponse::send(ApiResponse::error($e->getMessage()), 500);
    }
}
