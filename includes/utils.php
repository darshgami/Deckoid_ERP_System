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
