<?php
/**
 * Site Configuration
 * Accredited Inspection Agency Private Limited
 */

// Load environment variables from .env file if it exists
function load_env($path)
{
    if (!file_exists($path)) {
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0)
            continue;
        if (strpos($line, '=') === false)
            continue;

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
            putenv("$name=$value");
        }
    }
    return true;
}

// Load .env if exists
load_env(__DIR__ . '/../.env');

// Helper function to get env value with default
function env($key, $default = null)
{
    $value = getenv($key);
    return $value !== false ? $value : $default;
}

// Environment Detection
$is_production = env('ENVIRONMENT', 'development') === 'production';
$is_localhost = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1']);
define('IS_PRODUCTION', $is_production);
define('IS_LOCALHOST', $is_localhost);

// Error reporting (disable in production)
if (IS_PRODUCTION) {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Session configuration (optimized for low memory)
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 1800); // 30 minutes
    ini_set('session.cookie_httponly', 1);
    if (IS_PRODUCTION) {
        ini_set('session.cookie_secure', 1);
    }
    session_start();
}

// Site Information
define('SITE_NAME', 'Accredited Inspection Agency');
define('SITE_TAGLINE', 'Where Accuracy Meets Assurance');
define('SITE_EMAIL', 'info@accredited.co.in');
define('SITE_PHONE', '+91 - 8001480096');

define('SITE_ADDRESS', 'Near Khalsa Indan Gas Godam, Infront of Guru Dron School, Attarmuda Raigarh, Chhattisgarh - 496001');

// Base URL (auto-detect or from env)
if (IS_PRODUCTION) {
    define('BASE_URL', env('SITE_URL', 'https://accredited.co.in'));
} else {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $base_url = $protocol . '://' . ($_SERVER['SERVER_NAME'] ?? 'localhost');
    if (IS_LOCALHOST) {
        $base_url .= '/accredited';
    }
    define('BASE_URL', $base_url);
}

// Database Configuration (from env or defaults for XAMPP)
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_NAME', env('DB_NAME', 'agency_db'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', '')); // Empty for XAMPP default

// SMTP Configuration (from env or defaults)
define('SMTP_HOST', 'smtp.email.ap-hyderabad-1.oci.oraclecloud.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'ocid1.user.oc1..aaaaaaaajbuqw53mr4geclxjr7ijx4b6nivblsuvrm7m7h3rtzrnzoksgqua@ocid1.tenancy.oc1..aaaaaaaakytl7jbkemjnineq2yddfuschtr2nx3qjepztqox2steqk53dzga.7t.com');
define('SMTP_PASS', '_2pS!s;(Arx&L{gGC}-p'); // Update this on server
define('SMTP_FROM_EMAIL', 'info@accredited.co.in');
define('SMTP_FROM_NAME', 'Accredited Inspection Agency');
define('ADMIN_EMAIL', 'Amit@accredited.co.in');

// CSRF Token Generation
function generate_csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Input Sanitization
function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Flash Messages
function set_flash_message($type, $message)
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash_message()
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
