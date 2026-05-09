<?php
// Mock session for API call
session_start();
$_SESSION['user_id'] = 'mock-id'; 
$_SESSION['role'] = 'admin';

// We can't easily call the API file directly because of headers and exits
// But we can check the query logic

require_once 'config/env.php';
require_once 'includes/database.php';

$db = Database::getInstance();
$today = date('Y-m-d');

// Scenario: all followups
$where = ["next_followup_date IS NOT NULL"];
$params = [];
$whereClause = 'WHERE ' . implode(' AND ', $where);

$stmt = $db->prepare("SELECT COUNT(*) as total FROM leads $whereClause");
$stmt->execute($params);
$total = $stmt->fetchColumn();
echo "Total Followups (all): $total\n";

// Scenario: today
$where = ["next_followup_date IS NOT NULL", "next_followup_date = ?"];
$params = [$today];
$whereClause = 'WHERE ' . implode(' AND ', $where);
$stmt = $db->prepare("SELECT COUNT(*) as total FROM leads $whereClause");
$stmt->execute($params);
$total = $stmt->fetchColumn();
echo "Total Followups (today $today): $total\n";

// Scenario: upcoming
$where = ["next_followup_date IS NOT NULL", "next_followup_date > ?"];
$params = [$today];
$whereClause = 'WHERE ' . implode(' AND ', $where);
$stmt = $db->prepare("SELECT COUNT(*) as total FROM leads $whereClause");
$stmt->execute($params);
$total = $stmt->fetchColumn();
echo "Total Followups (upcoming): $total\n";
