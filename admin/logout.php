<?php
/**
 * Admin Logout
 */

require_once __DIR__ . '/../includes/config.php';

// Destroy session
session_unset();
session_destroy();

// Redirect to login
header('Location: login.php');
exit;
