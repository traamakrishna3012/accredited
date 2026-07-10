<?php
/**
 * Trigger Email Queue for a Batch
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/payroll_functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? '')) {
    die("Invalid request");
}

$batch_id = $_POST['batch_id'] ?? null;
if (!$batch_id) {
    die("Batch ID is required.");
}

// Ensure the batch exists
$stmt = $pdo->prepare("SELECT * FROM payroll_batches WHERE id = ?");
$stmt->execute([$batch_id]);
$batch = $stmt->fetch();

if (!$batch) {
    die("Batch not found.");
}

// Mark all payslips for this batch that have an email address as 'pending'
$stmt = $pdo->prepare("
    UPDATE payroll_payslips p
    JOIN payroll_employees e ON p.employee_id = e.id
    SET p.email_status = 'pending'
    WHERE p.batch_id = ? AND e.email IS NOT NULL AND e.email != ''
");
$stmt->execute([$batch_id]);

// Update batch status if not already
$stmt = $pdo->prepare("UPDATE payroll_batches SET status = 'emails_processing' WHERE id = ?");
$stmt->execute([$batch_id]);

set_flash_message('success', 'Emails have been queued. Processing will begin now.');
header("Location: payroll_email_processor.php?batch_id=" . $batch_id);
exit;
