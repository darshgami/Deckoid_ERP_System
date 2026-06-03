<?php

require_once 'config/env.php';

/**
 * Database Setup Script
 * Creates database, tables, and seeds sample data for Deckoid ERP
 */

class DatabaseSetup
{
    private $pdo;
    private $dbName;
    private $isLocal;

    public function __construct()
    {
        $this->dbName = Env::require('DB_DATABASE');
        $this->isLocal = (Env::get('APP_ENV', 'production') === 'local');

        try {
            // Connect to MySQL without specifying database first
            $dsn = sprintf(
                'mysql:host=%s;port=%s;charset=utf8mb4',
                Env::require('DB_HOST'),
                Env::require('DB_PORT')
            );

            $this->pdo = new PDO($dsn, Env::require('DB_USERNAME'), Env::get('DB_PASSWORD', ''));
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->log("Error: Connection failed - " . $e->getMessage(), "red");
            exit(1);
        }
    }

    public function run($force = false)
    {
        $this->log("==========================================", "cyan");
        $this->log("      DECKOID ERP - DATABASE SETUP        ", "cyan");
        $this->log("==========================================\n", "cyan");

        if (!$this->isLocal && !$force) {
            $this->log("WARNING: You are running this in a " . strtoupper(Env::get('APP_ENV')) . " environment.", "yellow");
            $this->log("This script will ERASE all data in the '{$this->dbName}' database.", "yellow");
            $this->log("If you are sure, run this with: php setup.php --force\n", "white");
            exit(1);
        }

        $this->dropDatabase();
        $this->createDatabase();
        $this->runMigrations();
        $this->seedData();

        $this->log("\n==========================================", "green");
        $this->log("   SETUP COMPLETED SUCCESSFULLY!          ", "green");
        $this->log("==========================================", "green");
        $this->log("URL: " . rtrim(Env::get('APP_URL', 'http://localhost'), '/') . '/login.php', "white");
        $this->log("Admin Login: admin / admin123\n", "white");
    }

    private function dropDatabase()
    {
        try {
            $this->pdo->exec("DROP DATABASE IF EXISTS `{$this->dbName}`");
            $this->log("✓ Database '{$this->dbName}' dropped.", "green");
        } catch (PDOException $e) {
            $this->log("! Failed to drop database: " . $e->getMessage(), "red");
        }
    }

    private function createDatabase()
    {
        try {
            $this->pdo->exec("CREATE DATABASE `{$this->dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->pdo->exec("USE `{$this->dbName}`");
            $this->log("✓ Database '{$this->dbName}' created.", "green");
        } catch (PDOException $e) {
            $this->log("! Failed to create database: " . $e->getMessage(), "red");
            exit(1);
        }
    }

    private function runMigrations()
    {
        $schemaFile = __DIR__ . '/database/schema.sql';
        if (!file_exists($schemaFile)) {
            $this->log("! Schema file not found.", "red");
            exit(1);
        }

        $sql = file_get_contents($schemaFile);
        
        // Remove comments
        $sql = preg_replace('/--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        
        // Split by semicolon, but try to handle it better
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        $count = 0;
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                try {
                    $this->pdo->exec($statement);
                    $count++;
                } catch (PDOException $e) {
                    // Ignore "Database already exists" errors if they happen inside the SQL file
                    if (strpos($e->getMessage(), 'database exists') === false) {
                        $this->log("! SQL Error: " . $e->getMessage(), "yellow");
                    }
                }
            }
        }
        $this->log("✓ Schema imported ({$count} statements executed).", "green");
    }

    private function seedData()
    {
        $this->log("\nSeeding sample data...", "cyan");

        // 1. Create Staff Users
        $staff = [
            ['id' => $this->generateUUID(), 'name' => 'Administrator', 'user' => 'admin', 'email' => 'admin@gmail.com', 'role' => 'admin', 'pass' => 'admin123'],
            ['id' => $this->generateUUID(), 'name' => 'Darsh Gami', 'user' => 'darsh', 'email' => 'darshgami1@gmail.com', 'role' => 'staff', 'pass' => 'darsh123'],
            ['id' => $this->generateUUID(), 'name' => 'Keval', 'user' => 'keval', 'email' => 'keval1@gmail.com', 'role' => 'staff', 'pass' => 'keval123'],
        ];

        $stmt = $this->pdo->prepare("INSERT INTO users (id, full_name, username, email, password_hash, role, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
        foreach ($staff as $s) {
            $hash = password_hash($s['pass'], PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt->execute([$s['id'], $s['name'], $s['user'], $s['email'], $hash, $s['role']]);
        }
        $this->log("✓ Staff accounts created.", "green");
    }

    private function generateUUID()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    private function log($message, $color = "white")
    {
        $colors = [
            "red" => "\033[31m",
            "green" => "\033[32m",
            "yellow" => "\033[33m",
            "blue" => "\033[34m",
            "cyan" => "\033[36m",
            "white" => "\033[0m",
        ];

        $c = $colors[$color] ?? $colors["white"];
        $reset = $colors["white"];
        
        echo "{$c}{$message}{$reset}\n";
    }
}

// Check for force flag
$force = in_array('--force', $argv);

$setup = new DatabaseSetup();
$setup->run($force);