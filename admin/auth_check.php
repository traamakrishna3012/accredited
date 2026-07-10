<?php
/**
 * Admin Authentication Check
 * Include this file at the top of all admin pages
 */

require_once __DIR__ . '/../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Regenerate session ID periodically for security
if (!isset($_SESSION['last_regeneration'])) {
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 1800) { // 30 minutes
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// Get current admin page
$admin_page = basename($_SERVER['PHP_SELF'], '.php');
