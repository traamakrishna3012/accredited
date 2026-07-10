<?php
/**
 * Commit Payroll Bulk Upload
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/payroll_functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? '')) {
    die("Invalid request");
}

$month = $_POST['month'] ?? '';
$standard_days = (float)($_POST['standard_days'] ?? 30);
$payload_json = $_POST['payload_json'] ?? '[]';
$data = json_decode($payload_json, true);

if (empty($month) || !is_array($data) || empty($data)) {
    set_flash_message('danger', 'Invalid submission data.');
    header("Location: payroll_upload.php");
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Delete existing batch for this month if it exists (cascade will delete payslips)
    $stmt = $pdo->prepare("DELETE FROM payroll_batches WHERE month = ?");
    $stmt->execute([$month]);

    // 2. Create new batch
    $stmt = $pdo->prepare("INSERT INTO payroll_batches (month, standard_working_days, status) VALUES (?, ?, 'draft')");
    $stmt->execute([$month, $standard_days]);
    $batch_id = $pdo->lastInsertId();

    // 3. Prepare insert statements
    $stmt_payslip = $pdo->prepare("
        INSERT INTO payroll_payslips 
        (batch_id, employee_id, work_days, lop_days, total_earnings, total_deductions, net_pay) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt_item = $pdo->prepare("
        INSERT INTO payroll_payslip_items 
        (payslip_id, component_id, standard_amount, actual_amount) 
        VALUES (?, ?, ?, ?)
    ");

    // 4. Insert records
    foreach ($data as $emp) {
        $stmt_payslip->execute([
            $batch_id,
            $emp['employee_id'],
            $emp['work_days'],
            $emp['lop_days'],
            $emp['earnings'],
            $emp['deductions'],
            $emp['net_pay']
        ]);
        $payslip_id = $pdo->lastInsertId();

        foreach ($emp['items'] as $item) {
            // Only insert if there's actually a standard or actual amount > 0 (saves DB space)
            if ($item['standard_amount'] > 0 || $item['actual_amount'] > 0 || $item['actual_amount'] < 0) {
                $stmt_item->execute([
                    $payslip_id,
                    $item['component_id'],
                    $item['standard_amount'],
                    $item['actual_amount']
                ]);
            }
        }
    }

    // 5. Log the upload
    $admin_id = $_SESSION['admin_id'] ?? null;
    $log_stmt = $pdo->prepare("INSERT INTO payroll_upload_logs (batch_id, uploaded_by, log_message) VALUES (?, ?, ?)");
    $log_stmt->execute([
        $batch_id,
        $admin_id,
        "Uploaded payroll for $month containing " . count($data) . " employees."
    ]);

    $pdo->commit();
    set_flash_message('success', "Successfully committed payroll for $month.");
    header("Location: payroll_batches.php"); // Assuming we'll build this in Phase 9, or can be dashboard.php
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    set_flash_message('danger', "Database error during commit: " . $e->getMessage());
    header("Location: payroll_upload.php");
    exit;
}
