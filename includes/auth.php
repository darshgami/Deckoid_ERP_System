<?php

require_once 'database.php';

class AuthController
{
    public static function register($data)
    {
        // Validate input
        if (!isset($data['full_name']) || !isset($data['email']) || !isset($data['username']) || !isset($data['password'])) {
            throw new Exception('Missing required fields');
        }

        $fullName = trim($data['full_name']);
        $email = trim($data['email']);
        $username = trim($data['username']);
        $password = $data['password'];

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

        // Hash password
        $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        // Generate UUID
        $id = self::generateUUID();

        // Insert user
        $stmt = $db->prepare("INSERT INTO users (id, full_name, email, username, password_hash) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$id, $fullName, $email, $username, $passwordHash]);

        return ['message' => 'User registered successfully', 'user_id' => $id];
    }

    public static function login($data)
    {
        if (!isset($data['username']) || !isset($data['password'])) {
            throw new Exception('Username and password are required');
        }

        $username = trim($data['username']);
        $password = $data['password'];

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, password_hash, status FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            throw new Exception('Invalid credentials');
        }

        if ($user['status'] !== 'active') {
            throw new Exception('Account is inactive');
        }

        // Generate tokens
        $accessToken = self::generateJWT($user['id']);
        $refreshToken = self::generateRefreshToken();

        // Store session
        $sessionId = self::generateUUID();
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));
        $stmt = $db->prepare("INSERT INTO sessions (id, user_id, refresh_token, ip_address, user_agent, expires_at) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $sessionId,
            $user['id'],
            $refreshToken,
            $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            $expiresAt
        ]);

        // Update last login
        $stmt = $db->prepare("UPDATE users SET last_login_at = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => 3600,
            'user' => [
                'id' => $user['id'],
                'username' => $username
            ]
        ];
    }

    public static function logout($data)
    {
        if (!isset($data['refresh_token'])) {
            throw new Exception('Refresh token is required');
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM sessions WHERE refresh_token = ?");
        $stmt->execute([$data['refresh_token']]);

        return ['message' => 'Logged out successfully'];
    }

    public static function refresh($data)
    {
        if (!isset($data['refresh_token'])) {
            throw new Exception('Refresh token is required');
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT user_id FROM sessions WHERE refresh_token = ? AND expires_at > NOW()");
        $stmt->execute([$data['refresh_token']]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$session) {
            throw new Exception('Invalid or expired refresh token');
        }

        // Generate new access token
        $accessToken = self::generateJWT($session['user_id']);

        return [
            'access_token' => $accessToken,
            'expires_in' => 3600
        ];
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

    private static function generateJWT($userId)
    {
        // Simple JWT implementation (in production, use a proper JWT library)
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode([
            'user_id' => $userId,
            'iat' => time(),
            'exp' => time() + 3600
        ]);

        $headerEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $payloadEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = hash_hmac('sha256', $headerEncoded . "." . $payloadEncoded, Env::get('APP_KEY', 'default_secret_key'), true);
        $signatureEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $headerEncoded . "." . $payloadEncoded . "." . $signatureEncoded;
    }

    private static function generateRefreshToken()
    {
        return bin2hex(random_bytes(32));
    }
}