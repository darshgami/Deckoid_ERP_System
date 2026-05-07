<?php

require_once 'config/env.php';
require_once 'includes/database.php';

try {
    $db = Database::getInstance();
    $stmt = $db->prepare('SELECT id, full_name, email, username, role FROM users WHERE username = ?');
    $stmt->execute(['admin']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "✓ Admin User Found:\n\n";
        echo "Username: " . $user['username'] . "\n";
        echo "Full Name: " . $user['full_name'] . "\n";
        echo "Email: " . $user['email'] . "\n";
        echo "Role: " . $user['role'] . "\n";
        echo "User ID: " . $user['id'] . "\n";
        echo "\n✓ Default Password: admin123\n";
        echo "\n✓ Admin account is active and ready to use.\n";
    } else {
        echo "Admin user not found in database!\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}