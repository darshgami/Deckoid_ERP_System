<?php
require_once 'includes/database.php';

try {
    $db = Database::getInstance();
    
    $tables = ['users', 'sessions', 'leads', 'lead_activity_logs'];
    
    foreach ($tables as $table) {
        echo "\n--- Table: $table ---\n";
        $stmt = $db->query("SHOW INDEX FROM $table");
        $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($indexes as $index) {
            echo "Index: " . $index['Key_name'] . " | Column: " . $index['Column_name'] . "\n";
        }
        
        $stmt = $db->query("DESCRIBE $table");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $column) {
            echo "Field: " . $column['Field'] . " | Type: " . $column['Type'] . " | Null: " . $column['Null'] . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
