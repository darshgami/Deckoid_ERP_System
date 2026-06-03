<?php
require_once 'config/env.php';
require_once 'includes/database.php';
$db = Database::getInstance();
$stmt = $db->query('DESCRIBE leads');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
