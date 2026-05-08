<?php
/**
 * Migration: Fix lead_activity_logs
 * - Adds `company_client_name` column (stores name at log-time, survives lead deletion)
 * - Changes FK `lead_id` from ON DELETE CASCADE → ON DELETE SET NULL (nullable)
 */

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../includes/database.php';

$db = Database::getInstance();

echo "<pre>\n";

try {
    // Step 1: Drop FK constraint (MySQL requires knowing constraint name)
    $fkResult = $db->query("
        SELECT CONSTRAINT_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'lead_activity_logs' 
          AND COLUMN_NAME = 'lead_id'
          AND REFERENCED_TABLE_NAME = 'leads'
    ")->fetch(PDO::FETCH_ASSOC);

    if ($fkResult) {
        $fkName = $fkResult['CONSTRAINT_NAME'];
        $db->exec("ALTER TABLE lead_activity_logs DROP FOREIGN KEY `$fkName`");
        echo "✅ Dropped FK: $fkName\n";
    } else {
        echo "ℹ️ No FK found on lead_id (already removed or named differently)\n";
    }

    // Step 2: Make lead_id nullable
    $db->exec("ALTER TABLE lead_activity_logs MODIFY COLUMN lead_id CHAR(36) NULL");
    echo "✅ Made lead_id nullable\n";

    // Step 3: Re-add FK with ON DELETE SET NULL
    $db->exec("
        ALTER TABLE lead_activity_logs 
        ADD CONSTRAINT fk_log_lead_id 
        FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE SET NULL
    ");
    echo "✅ Re-added FK with ON DELETE SET NULL\n";

    // Step 4: Add company_client_name column if not exists
    $colCheck = $db->query("
        SELECT COLUMN_NAME FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
          AND TABLE_NAME = 'lead_activity_logs' 
          AND COLUMN_NAME = 'company_client_name'
    ")->fetch();

    if (!$colCheck) {
        $db->exec("ALTER TABLE lead_activity_logs ADD COLUMN company_client_name VARCHAR(255) NULL AFTER user_id");
        echo "✅ Added company_client_name column\n";
    } else {
        echo "ℹ️ company_client_name column already exists\n";
    }

    echo "\n🎉 Migration complete! Activity logs will now persist even after leads are deleted.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "</pre>";
