<?php

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/utils.php';

class AuthController
{
    /**
     * Verify user password against supported legacy formats and migrate to bcrypt when needed.
     */
    private static function verifyAndUpgradePassword($db, array $user, $plainPassword)
    {
        $storedHash = (string)($user['password_hash'] ?? '');

        if ($storedHash === '') {
            return false;
        }

        // Preferred path: modern password_hash() formats (bcrypt/argon).
        if (password_verify($plainPassword, $storedHash)) {
            if (password_needs_rehash($storedHash, PASSWORD_BCRYPT, ['cost' => 12])) {
                $newHash = password_hash($plainPassword, PASSWORD_BCRYPT, ['cost' => 12]);
                $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                $stmt->execute([$newHash, $user['id']]);
            }
            return true;
        }

        // Backward compatibility for legacy production data formats.
        $legacyMatched =
            hash_equals($storedHash, $plainPassword) ||
            hash_equals($storedHash, md5($plainPassword)) ||
            hash_equals($storedHash, sha1($plainPassword)) ||
            hash_equals($storedHash, hash('sha256', $plainPassword));

        if (!$legacyMatched) {
            return false;
        }

        // Auto-upgrade legacy hash/plaintext to bcrypt after successful validation.
        $newHash = password_hash($plainPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->execute([$newHash, $user['id']]);

        return true;
    }

    /**
     * Register a new user
     * Uses BCRYPT for password hashing as per requirement
     */
    public static function register($data)
    {
        // Start session if not started
        start_secure_session();

        // Professional Validation
        $validator = new Validator();
        $rules = [
            'full_name' => 'required|min:3',
            'email' => 'required|email',
            'username' => 'required|min:3',
            'password' => 'required|min:8',
            'role' => 'required'
        ];

        if (!$validator->validate($data, $rules)) {
            throw new Exception($validator->getFirstError());
        }

        $fullName = trim($data['full_name']);
        $email = trim($data['email']);
        $username = trim($data['username']);
        $password = $data['password'];
        $role = $data['role'] ?? 'staff';

        // Check if username already exists
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            throw new Exception("Username '$username' is already taken.");
        }

        // Check if email already exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            throw new Exception("Email address '$email' is already registered.");
        }

        // Hash password using BCRYPT
        $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        // Generate UUID
        $id = generateUUID();

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
        $stmt = $db->prepare("SELECT id, username, password_hash, status, role, full_name FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !self::verifyAndUpgradePassword($db, $user, $password)) {
            throw new Exception('Invalid credentials');
        }

        if ($user['status'] !== 'active') {
            throw new Exception('Account is inactive');
        }

        // Regenerate session ID to prevent fixation
        secure_session_regenerate();

        // Store user info in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'] ?? $username;
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['last_activity'] = time();

        // Update last login
        $stmt = $db->prepare("UPDATE users SET last_login_at = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);

        // Record session in database
        $dbSessionId = generateUUID();
        $_SESSION['db_session_id'] = $dbSessionId; // Store for logout cleanup
        $refreshToken = bin2hex(random_bytes(32));
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $expires = date('Y-m-d H:i:s', time() + Env::get('SESSION_TIMEOUT', 3600));

        $sessionStmt = $db->prepare("INSERT INTO sessions (id, user_id, refresh_token, ip_address, user_agent, expires_at) VALUES (?, ?, ?, ?, ?, ?)");
        $sessionStmt->execute([$dbSessionId, $user['id'], $refreshToken, $ip, $ua, $expires]);

        return [
            'message' => 'Login successful',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'] ?? $username,
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

        // Clean up database session
        if (isset($_SESSION['db_session_id'])) {
            $db = Database::getInstance();
            $stmt = $db->prepare("DELETE FROM sessions WHERE id = ?");
            $stmt->execute([$_SESSION['db_session_id']]);
        }

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
}