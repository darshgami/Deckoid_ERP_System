<?php

require_once 'config/env.php';

/**
 * Database Setup Script
 * Creates database and tables without using Composer/Phinx
 */

class DatabaseSetup
{
    private $pdo;

    public function __construct()
    {
        try {
            // Connect to MySQL without specifying database first
            $dsn = sprintf(
                'mysql:host=%s;port=%s;charset=utf8mb4',
                Env::require('DB_HOST'),
                Env::require('DB_PORT')
            );

            $this->pdo = new PDO($dsn, Env::require('DB_USERNAME'), Env::get('DB_PASSWORD', ''));
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage() . PHP_EOL);
        }
    }

    public function dropDatabase()
    {
        $database = Env::require('DB_DATABASE');

        try {
            $this->pdo->exec("DROP DATABASE IF EXISTS `$database`");
            echo "Database '$database' dropped successfully.\n";
        } catch (PDOException $e) {
            die('Failed to drop database: ' . $e->getMessage() . PHP_EOL);
        }
    }

    public function createDatabase()
    {
        $database = Env::require('DB_DATABASE');

        try {
            $this->pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            echo "Database '$database' created successfully.\n";

            // Switch to the database
            $this->pdo->exec("USE `$database`");
        } catch (PDOException $e) {
            die('Failed to create database: ' . $e->getMessage() . PHP_EOL);
        }
    }

    public function runMigrations()
    {
        $database = Env::require('DB_DATABASE');
        $this->pdo->exec("USE `$database`");

        $schemaFile = __DIR__ . '/database/schema.sql';

        if (!file_exists($schemaFile)) {
            die('Schema file not found: ' . $schemaFile . PHP_EOL);
        }

        $sql = file_get_contents($schemaFile);

        try {
            // Remove comments (single line and multi-line)
            $sql = preg_replace('/--.*$/m', '', $sql);
            $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
            
            // Split SQL into individual statements
            $statements = array_filter(array_map('trim', explode(';', $sql)));

            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    $this->pdo->exec($statement);
                }
            }

            echo "All tables created successfully.\n";
        } catch (PDOException $e) {
            die('Migration failed: ' . $e->getMessage() . PHP_EOL);
        } catch (Exception $e) {
            die('Unexpected error: ' . $e->getMessage() . PHP_EOL);
        }
    }

    public function seedSampleData()
    {
        $database = Env::require('DB_DATABASE');
        $this->pdo->exec("USE `$database`");

        try {
            // Check if admin user already exists
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = 'admin'");
            $stmt->execute();
            $existing = $stmt->fetch();

            if ($existing) {
                echo "Admin user already exists.\n";
                return;
            }

            // Create a sample admin user
            $passwordHash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);
            $userId = $this->generateUUID();

            $stmt = $this->pdo->prepare("INSERT INTO users (id, full_name, email, username, password_hash, role, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, 'Administrator', 'admin@example.com', 'admin', $passwordHash, 'admin', 'active']);

            echo "Sample admin user created successfully.\n";
            echo "Username: admin\n";
            echo "Password: admin123\n";
        } catch (PDOException $e) {
            echo 'Warning: Could not create sample user: ' . $e->getMessage() . PHP_EOL;
        }
    }

    private function generateUUID()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}

// Run setup
echo "Starting database setup...\n";

$setup = new DatabaseSetup();
$setup->dropDatabase();
$setup->createDatabase();
$setup->runMigrations();
$setup->seedSampleData();

echo "\nDatabase setup completed successfully!\n";
echo "You can now access the application at: http://localhost/login.php\n";