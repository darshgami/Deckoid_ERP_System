<?php
require_once 'config/env.php';
require_once 'includes/database.php';
$db = Database::getInstance();
$stmt = $db->query('SELECT COUNT(*) FROM leads');
echo "COUNT: " . $stmt->fetchColumn() . "\n";
