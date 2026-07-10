<?php
require_once __DIR__ . '/includes/db.php';

$username = 'admin';
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        // Update
        $update = $pdo->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
        $update->execute([$hash, $username]);
        echo "Password updated for user '$username'.";
    } else {
        // Create
        $insert = $pdo->prepare("INSERT INTO admin_users (username, password, email) VALUES (?, ?, 'admin@accredited.co.in')");
        $insert->execute([$username, $hash]);
        echo "User '$username' created with password.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
