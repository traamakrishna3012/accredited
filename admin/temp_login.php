<?php
session_start();
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_id'] = 1;
$_SESSION['admin_username'] = 'admin';
header('Location: dashboard.php');
exit;
