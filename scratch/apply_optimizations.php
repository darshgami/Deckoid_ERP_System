<?php
require_once 'includes/database.php';

try {
    $db = Database::getInstance();
    
    echo "Applying indexes...\n";
    $db->exec("CREATE INDEX IF NOT EXISTS idx_logs_created_at ON lead_activity_logs(created_at)");
    echo "✓ Index on lead_activity_logs(created_at) applied.\n";
    
    $db->exec("CREATE INDEX IF NOT EXISTS idx_leads_deal_status ON leads(deal_status)");
    echo "✓ Index on leads(deal_status) applied.\n";
    
    echo "\nAll optimizations applied directly to the database!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
