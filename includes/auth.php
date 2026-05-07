<?php

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/session.php';

class AuthController
{
    /**
     * Register a new user
     * Uses BCRYPT for password hashing as per requirement
     */
    public static function register($data)
    {
        // Start session if not started
        start_secure_session();

        // Validate input
        if (!isset($data['full_name']) || !isset($data['email']) || !isset($data['username']) || !isset($data['password'])) {
            throw new Exception('Missing required fields');
        }

        $fullName = trim($data['full_name']);
        $email = trim($data['email']);
        $username = trim($data['username']);
        $password = $data['password'];
        $role = $data['role'] ?? 'staff'; // Default to staff

        if (empty($fullName) || empty($email) || empty($username) || empty($password)) {
            throw new Exception('All fields are required');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }

        if (strlen($password) < 8) {
            throw new Exception('Password must be at least 8 characters');
        }

        // Check if user already exists
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        if ($stmt->fetch()) {
            throw new Exception('User already exists');
        }

        // Hash password using BCRYPT
        $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        // Generate UUID
        $id = self::generateUUID();

        // Insert user
        $stmt = $db->prepare("INSERT INTO users (id, full_name, email, username, password_hash, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id, $fullName, $email, $username, $passwordHash, $role]);

        return ['message' => 'User registered successfully', 'user_id' => $id];
    }

    /**
     * Login user and establish session
     * Implements session regeneration for security
     */
    public static function login($data)
    {
        // Start session
        start_secure_session();

        if (!isset($data['username']) || !isset($data['password'])) {
            throw new Exception('Username and password are required');
        }

        $username = trim($data['username']);
        $password = $data['password'];

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, password_hash, status, role, full_name FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            throw new Exception('Invalid credentials');
        }

        if ($user['status'] !== 'active') {
            throw new Exception('Account is inactive');
        }

        // Regenerate session ID to prevent fixation
        secure_session_regenerate();

        // Store user info in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['last_activity'] = time();

        // Update last login
        $stmt = $db->prepare("UPDATE users SET last_login_at = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);

        return [
            'message' => 'Login successful',
            'user' => [
                'id' => $user['id'],
                'username' => $username,
                'role' => $user['role']
            ]
        ];
    }

    /**
     * Logout user and destroy session
     */
    public static function logout()
    {
        start_secure_session();

        // Unset all session variables
        $_SESSION = array();

        // Destroy cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Destroy session
        session_destroy();

        return ['message' => 'Logged out successfully'];
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn()
    {
        start_secure_session();
        return isset($_SESSION['user_id']);
    }

    /**
     * Get current user role
     */
    public static function getCurrentRole()
    {
        start_secure_session();
        return $_SESSION['role'] ?? null;
    }

    private static function generateUUID()
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