<?php

/**
 * Quick Test Script
 * Tests basic functionality without setting up the full database
 */

require_once 'config/env.php';

echo "=== Lead Management ERP System Test ===\n\n";

try {
    // Test environment loading
    echo "1. Testing environment configuration...\n";
    $appName = Env::get('APP_NAME');
    $dbHost = Env::get('DB_HOST');
    echo "   ✓ Environment loaded: APP_NAME = $appName\n";
    echo "   ✓ Database host: $dbHost\n";

    // Test database connection (without database name)
    echo "\n2. Testing database connection...\n";
    $dsn = sprintf('mysql:host=%s;port=%s;charset=utf8mb4', Env::require('DB_HOST'), Env::require('DB_PORT'));
    $pdo = new PDO($dsn, Env::require('DB_USERNAME'), Env::get('DB_PASSWORD', ''));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "   ✓ MySQL connection successful\n";

    // Test if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE '" . Env::require('DB_DATABASE') . "'");
    $dbExists = $stmt->fetch();

    if ($dbExists) {
        echo "   ✓ Database '" . Env::require('DB_DATABASE') . "' exists\n";

        // Test if tables exist
        $pdo->exec("USE " . Env::require('DB_DATABASE'));
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (in_array('users', $tables)) {
            echo "   ✓ Users table exists\n";
        } else {
            echo "   ⚠ Users table not found - run setup.php\n";
        }

        if (in_array('leads', $tables)) {
            echo "   ✓ Leads table exists\n";
        } else {
            echo "   ⚠ Leads table not found - run setup.php\n";
        }
    } else {
        echo "   ⚠ Database '" . Env::require('DB_DATABASE') . "' does not exist - run setup.php\n";
    }

    // Test file permissions
    echo "\n3. Testing file permissions...\n";

    $dirs = ['exports', 'uploads', 'admin', 'api', 'includes'];
    foreach ($dirs as $dir) {
        if (is_dir($dir)) {
            echo "   ✓ Directory '$dir' exists\n";
            if (is_writable($dir)) {
                echo "   ✓ Directory '$dir' is writable\n";
            } else {
                echo "   ⚠ Directory '$dir' is not writable\n";
            }
        } else {
            echo "   ⚠ Directory '$dir' does not exist\n";
        }
    }

    echo "\n=== Test completed ===\n";

    if ($dbExists && in_array('users', $tables ?? []) && in_array('leads', $tables ?? [])) {
        echo "\n🎉 System is ready! Access it at: http://localhost/admin/login.php\n";
        echo "Default login: admin / admin123\n";
    } else {
        echo "\n⚠️  Please run 'php setup.php' to complete the setup.\n";
    }

} catch (Exception $e) {
    echo "\n❌ Test failed: " . $e->getMessage() . "\n";
    echo "\nTroubleshooting:\n";
    echo "- Make sure XAMPP MySQL is running\n";
    echo "- Check database credentials in .env file\n";
    echo "- Run 'php setup.php' to set up the database\n";
}