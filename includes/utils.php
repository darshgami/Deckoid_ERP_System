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
 * Get the application base path from APP_URL or the current request.
 */
function app_base_path() {
    static $basePath = null;

    if ($basePath !== null) {
        return $basePath;
    }

    if (class_exists('Env')) {
        $appUrl = trim((string) Env::get('APP_URL', ''));
        if ($appUrl !== '') {
            $path = parse_url($appUrl, PHP_URL_PATH);
            $basePath = rtrim((string) $path, '/');

            if ($basePath === '/' || $basePath === '.' || $basePath === '\\') {
                $basePath = '';
            }

            return $basePath;
        }
    }

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
