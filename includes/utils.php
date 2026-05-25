<?php
/**
 * Utility Functions for Deckoid ERP
 */

/**
 * Generate a cryptographically secure UUID v4
 * @return string
 */
function generateUUID() {
    $data = random_bytes(16);
    
    // Set version to 4 (0100)
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    
    return vsprintf('%02s%02s%02s%02s-%02s%02s-%02s%02s-%02s%02s-%02s%02s%02s%02s%02s%02s', 
        str_split(bin2hex($data), 2));
}

/**
 * Standard API Response Helper
 */
class ApiResponse {
    public static function success($message, $data = []) {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
    }

    public static function error($message) {
        return [
            'success' => false,
            'error' => $message,
            'message' => $message
        ];
    }

    public static function send($response, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

/**
 * Detect HTTPS reliably on shared hosting and reverse proxies.
 */
function is_https_request() {
    if (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off') {
        return true;
    }

    if (!empty($_SERVER['SERVER_PORT']) && (string)$_SERVER['SERVER_PORT'] === '443') {
        return true;
    }

    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && stripos((string)$_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') !== false) {
        return true;
    }

    if (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower((string)$_SERVER['HTTP_X_FORWARDED_SSL']) === 'on') {
        return true;
    }

    return false;
}

/**
 * Apply CORS headers safely for APIs, including credentialed requests.
 */
function apply_api_cors_headers($allowedMethods = 'GET, POST, PUT, PATCH, DELETE, OPTIONS') {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allowedOrigin = '';

    if (!empty($origin)) {
        $originHost = parse_url($origin, PHP_URL_HOST);
        $requestHost = $_SERVER['HTTP_HOST'] ?? '';
        $requestHost = preg_replace('/:\\d+$/', '', (string)$requestHost);

        if (!empty($originHost) && !empty($requestHost) && strcasecmp($originHost, $requestHost) === 0) {
            $allowedOrigin = $origin;
        }

        if ($allowedOrigin === '' && class_exists('Env')) {
            $appUrl = Env::get('APP_URL', '');
            if (!empty($appUrl)) {
                $appHost = parse_url($appUrl, PHP_URL_HOST);
                if (!empty($appHost) && !empty($originHost) && strcasecmp($appHost, $originHost) === 0) {
                    $allowedOrigin = $origin;
                }
            }
        }
    }

    if ($allowedOrigin !== '') {
        header('Access-Control-Allow-Origin: ' . $allowedOrigin);
        header('Access-Control-Allow-Credentials: true');
        header('Vary: Origin');
    } else {
        header('Access-Control-Allow-Origin: *');
    }

    header('Access-Control-Allow-Methods: ' . $allowedMethods);
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
}

/**
 * Production-grade Logger
 */
class Logger {
    private static $logPath = __DIR__ . '/../logs/app.log';

    public static function info($message, $context = []) {
        self::log('INFO', $message, $context);
    }

    public static function warn($message, $context = []) {
        self::log('WARN', $message, $context);
    }

    public static function error($message, $context = []) {
        self::log('ERROR', $message, $context);
    }

    public static function debug($message, $context = []) {
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            self::log('DEBUG', $message, $context);
        }
    }

    private static function log($level, $message, $context) {
        $logDir = dirname(self::$logPath);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logMessage = "[$timestamp] [$level] $message$contextStr" . PHP_EOL;
        
        error_log($logMessage, 3, self::$logPath);

        // Professional terminal output (simulated for PHP CLI or server logs)
        if (php_sapi_name() === 'cli') {
            echo $logMessage;
        }
    }
}

/**
 * Sanitize output for HTML
 * @param string $string
 * @return string
 */
function h($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Format currency
 * @param float $amount
 * @return string
 */
function formatCurrency($amount) {
    return '₹' . number_format($amount ?? 0, 2);
}

/**
 * Format date to DD/MM/YYYY
 * @param string $date
 * @return string
 */
function formatDate($date) {
    if (!$date) return '';
    return date('d/m/Y', strtotime($date));
}

/**
 * Get the application base path from APP_URL or the current request.
 */
function app_base_path() {
    static $basePath = null;

    if ($basePath !== null) {
        return $basePath;
    }

    // Calculate base path dynamically based on actual script execution path
    // This ensures it works seamlessly across any local folder name or production environment

    $scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
    $scriptDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
    $knownAppSections = ['admin', 'api', 'includes', 'assets', 'config', 'database', 'docs', 'scratch'];

    if ($scriptDir === '' || $scriptDir === '.') {
        $basePath = '';
    } elseif (in_array(basename($scriptDir), $knownAppSections, true)) {
        $basePath = rtrim(dirname($scriptDir), '/');
    } else {
        $basePath = $scriptDir;
    }

    if ($basePath === '/' || $basePath === '.' || $basePath === '\\') {
        $basePath = '';
    }

    return $basePath;
}

/**
 * Build an application-relative asset URL.
 */
function asset_url($path) {
    $path = ltrim((string) $path, '/');
    $basePath = trim(app_base_path(), '/');

    return '/' . ($basePath !== '' ? $basePath . '/' : '') . $path;
}

/**
 * CSRF Protection Helpers
 */
function csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}

/**
 * Professional ERP Validator Class
 */
class Validator {
    private $errors = [];

    /**
     * Validate data against specified rules
     * Rules format: 'field_name' => 'required|email|min:3'
     */
    public function validate($data, $rules) {
        foreach ($rules as $field => $fieldRules) {
            $value = isset($data[$field]) ? trim((string)$data[$field]) : '';
            $fieldRulesArray = explode('|', $fieldRules);

            foreach ($fieldRulesArray as $rule) {
                // Required check
                if ($rule === 'required' && ($value === '')) {
                    $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . " is required");
                    break; // Skip further rules if empty
                }

                if ($value === '') continue; // Skip other rules if empty and not required

                // Email check
                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "Invalid email format");
                }

                // Numeric check
                if ($rule === 'numeric' && !is_numeric($value)) {
                    $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . " must be a numeric value");
                }

                // Min length check
                if (strpos($rule, 'min:') === 0) {
                    $min = (int)substr($rule, 4);
                    if (strlen($value) < $min) {
                        $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . " must be at least $min characters");
                    }
                }

                // Mobile number check
                if ($rule === 'mobile' && !preg_match('/^[0-9]{10,15}$/', $value)) {
                    $this->addError($field, "Invalid mobile number (10-15 digits)");
                }
                
                // GSTIN format check
                if ($rule === 'gstin' && !preg_match('/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/', $value)) {
                    $this->addError($field, "Invalid GSTIN format");
                }
            }
        }
        return empty($this->errors);
    }

    public function addError($field, $message) {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }

    public function getErrors() {
        return $this->errors;
    }

    public function getFirstError() {
        return !empty($this->errors) ? reset($this->errors) : null;
    }
}
