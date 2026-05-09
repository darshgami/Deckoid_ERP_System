<?php
require_once 'config/env.php';
try {
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', Env::require('DB_HOST'), Env::require('DB_DATABASE'));
    $pdo = new PDO($dsn, Env::require('DB_USERNAME'), Env::get('DB_PASSWORD', ''));
    $stmt = $pdo->query('SELECT company_client_name, next_followup_date FROM leads WHERE next_followup_date IS NOT NULL');
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Found " . count($results) . " leads with followups.\n";
    print_r($results);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
