<?php
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once '../includes/middleware.php';
require_once '../includes/database.php';
require_once '../includes/utils.php';

Logger::info('Import script started.');

// Ensure user is logged in
requireAuth();
$db = Database::getInstance();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Failed to upload file.');
    }

    $file = $_FILES['file'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    
    if (strtolower($ext) !== 'csv') {
        throw new Exception('Only CSV files are allowed.');
    }

    // Suppress PHP warnings for fgetcsv if file is weird
    $handle = @fopen($file['tmp_name'], 'r');
    if (!$handle) {
        throw new Exception('Unable to read the uploaded file.');
    }

    $headers = fgetcsv($handle);
    if (!$headers) {
        throw new Exception('Empty or invalid CSV file.');
    }

    // Expected headers from old CSV backups
    $expectedHeaders = [
        'Lead ID', 'Lead Date', 'Company', 'Contact Person', 'Mobile Number', 
        'Email ID', 'City', 'State', 'Lead Category', 'Lead Status', 
        'Assigned To', 'Next Follow-up Date', 'Estimated Budget', 'Payment Status', 
        'Reference By', 'Remarks'
    ];

    // Mapping to database columns
    $dbMapping = [
        'Lead ID' => 'lead_id',
        'Lead Date' => 'lead_date',
        'Company' => 'company',
        'Contact Person' => 'contact_person',
        'Mobile Number' => 'mobile_number',
        'Email ID' => 'email_id',
        'City' => 'city',
        'State' => 'state',
        'Lead Category' => 'lead_category',
        'Lead Status' => 'lead_status',
        'Assigned To' => 'assigned_to', // This might need user ID lookup if it has names, but we'll map direct for now
        'Next Follow-up Date' => 'next_followup_date',
        'Estimated Budget' => 'estimated_budget',
        'Payment Status' => 'payment_status',
        'Reference By' => 'reference_by',
        'Remarks' => 'remarks'
    ];

    // Normalize headers to remove BOM and whitespace
    $headers = array_map(function($header) {
        $header = trim($header);
        // Remove UTF-8 BOM if present
        if (substr($header, 0, 3) === "\xEF\xBB\xBF") {
            $header = substr($header, 3);
        }
        return $header;
    }, $headers);
    
    // We expect these columns (Lead ID is ignored during insert, so we don't strictly require it to be present)
    $missingHeaders = array_diff($expectedHeaders, $headers);
    
    // It's okay if Lead ID is missing since we auto-generate it
    if (($key = array_search('Lead ID', $missingHeaders)) !== false) {
        unset($missingHeaders[$key]);
    }

    if (!empty($missingHeaders)) {
        throw new Exception('Missing required columns: ' . implode(', ', $missingHeaders));
    }

    $headerMap = array_flip($headers);

    // Verify session user exists in database (prevents FK constraint errors from phantom sessions after DB resets)
    $createdBy = $_SESSION['user_id'] ?? null;
    if ($createdBy) {
        $chkUser = $db->prepare("SELECT id FROM users WHERE id = ?");
        $chkUser->execute([$createdBy]);
        if (!$chkUser->fetch()) {
            $createdBy = null; // User doesn't exist in DB anymore, use NULL to avoid FK error
        }
    }

    $db->beginTransaction();
    $insertedCount = 0;
    $errors = [];
    $rowNumber = 1;

    // Get the highest lead_id number for generating new ones
    $lastIdStmt = $db->query("SELECT lead_id FROM leads WHERE lead_id LIKE 'DK%' ORDER BY lead_id DESC LIMIT 1");
    $lastLead = $lastIdStmt->fetch(PDO::FETCH_ASSOC);
    $highestNum = $lastLead ? ((int)substr($lastLead['lead_id'], 2)) : 0;

    while (($row = fgetcsv($handle)) !== false) {
        $rowNumber++;
        if (empty(array_filter($row))) continue; // Skip empty rows

        $rowData = [];
        foreach ($expectedHeaders as $header) {
            $val = trim($row[$headerMap[$header]] ?? '');
            $rowData[$dbMapping[$header]] = $val === '' ? null : $val;
        }

        // We do NOT check for duplicates. We just validate the bare minimum.
        if (empty($rowData['mobile_number'])) {
            $errors[] = "Failed Row:\nRow $rowNumber\nmobile_number\nMobile number is required";
            continue;
        }
        if (empty($rowData['company'])) {
            $errors[] = "Failed Row:\nRow $rowNumber\ncompany\nCompany Name is required";
            continue;
        }
        if (empty($rowData['lead_date'])) {
            $rowData['lead_date'] = date('Y-m-d');
        }

        // Date format conversion if necessary (some old CSVs use DD-MM-YY)
        if (!empty($rowData['lead_date']) && preg_match('/^\d{2}-\d{2}-\d{2,4}$/', $rowData['lead_date'])) {
            $dateParts = explode('-', $rowData['lead_date']);
            $year = strlen($dateParts[2]) == 2 ? '20' . $dateParts[2] : $dateParts[2];
            $rowData['lead_date'] = $year . '-' . $dateParts[1] . '-' . $dateParts[0];
        }
        if (!empty($rowData['next_followup_date']) && preg_match('/^\d{2}-\d{2}-\d{2,4}$/', $rowData['next_followup_date'])) {
            $dateParts = explode('-', $rowData['next_followup_date']);
            $year = strlen($dateParts[2]) == 2 ? '20' . $dateParts[2] : $dateParts[2];
            $rowData['next_followup_date'] = $year . '-' . $dateParts[1] . '-' . $dateParts[0];
        }

        // Assigned To mapping (Name to ID)
        if (!empty($rowData['assigned_to'])) {
            $uStmt = $db->prepare("SELECT id FROM users WHERE full_name = ? OR username = ?");
            $uStmt->execute([$rowData['assigned_to'], $rowData['assigned_to']]);
            $u = $uStmt->fetch();
            $rowData['assigned_to'] = $u ? $u['id'] : null;
        }

        // Always Insert
        $id = generateUUID();
        
        $highestNum++;
        $leadId = 'DK' . str_pad($highestNum, 4, '0', STR_PAD_LEFT);
        
        $columns = ['id', 'lead_id', 'created_by'];
        $values = [$id, $leadId, $createdBy];

        foreach ($dbMapping as $header => $dbCol) {
            if (in_array($dbCol, ['lead_id', 'created_at', 'updated_at'])) {
                continue; 
            }
            $columns[] = $dbCol;
            $values[] = $rowData[$dbCol];
        }
        
        $placeholders = implode(', ', array_fill(0, count($values), '?'));
        
        $insertSql = "INSERT INTO leads (" . implode(', ', $columns) . ") VALUES ($placeholders)";
        $inStmt = $db->prepare($insertSql);
        
        try {
            $inStmt->execute($values);
            $insertedCount++;
        } catch (PDOException $ex) {
            $errors[] = "Failed Row:\nRow $rowNumber\nDatabase Insert\n" . $ex->getMessage();
        }
    }
    
    fclose($handle);

    if (empty($errors)) {
        $db->commit();
        echo json_encode(["success" => true, "message" => "Imported $insertedCount records successfully"]);
    } else {
        $db->rollBack();
        $msg = implode("\n\n", $errors);
        // Ensure UTF-8 to prevent json_encode failure
        $msg = mb_convert_encoding($msg, 'UTF-8', 'UTF-8'); 
        echo json_encode(["success" => false, "message" => $msg]);
    }

} catch (Throwable $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    $msg = $e->getMessage();
    // Log the actual error to see what's happening
    Logger::error('CSV Import Crash: ' . $msg);
    // Ensure UTF-8
    $msg = mb_convert_encoding($msg, 'UTF-8', 'UTF-8');
    echo json_encode(["success" => false, "message" => $msg]);
}
