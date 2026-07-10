<?php
/**
 * Payroll Email Processor
 * Processes queue in batches to avoid timeout
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/payroll_functions.php';
require_once __DIR__ . '/../includes/payroll_pdf_generator.php';
require_once __DIR__ . '/../api/send_email.php';

$batch_id = $_GET['batch_id'] ?? null;
if (!$batch_id) {
    die("Batch ID is required.");
}

// Fetch 5 pending emails
$stmt = $pdo->prepare("
    SELECT p.id as payslip_id, e.name, e.email, b.month
    FROM payroll_payslips p
    JOIN payroll_employees e ON p.employee_id = e.id
    JOIN payroll_batches b ON p.batch_id = b.id
    WHERE p.batch_id = ? AND p.email_status = 'pending' AND e.email IS NOT NULL AND e.email != ''
    LIMIT 5
");
$stmt->execute([$batch_id]);
$pending = $stmt->fetchAll();

$processed_count = 0;
$tmp_dir = sys_get_temp_dir();

if (count($pending) > 0) {
    foreach ($pending as $row) {
        $payslip_id = $row['payslip_id'];
        $email = trim($row['email']);
        $name = $row['name'];
        $month_formatted = date('F Y', strtotime($row['month'] . '-01'));

        // Generate PDF
        $filename = '';
        $dompdf = generate_payslip_pdf($pdo, $payslip_id, $filename);
        
        if ($dompdf && $filename && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $pdf_content = $dompdf->output();
            $file_path = $tmp_dir . '/' . $filename;
            file_put_contents($file_path, $pdf_content);

            // Send Email
            $subject = "Your Payslip for " . $month_formatted;
            $body = "<p>Dear $name,</p><p>Please find attached your payslip for the month of $month_formatted.</p><p>Regards,<br>" . (defined('SITE_NAME') ? SITE_NAME : 'Accredited Inspection Agency') . "</p>";

            $sent = send_email($email, $name, $subject, $body, true, $file_path);

            // Clean up temp file
            @unlink($file_path);

            // Update status
            $status = $sent ? 'sent' : 'failed';
            $upd = $pdo->prepare("UPDATE payroll_payslips SET email_status = ? WHERE id = ?");
            $upd->execute([$status, $payslip_id]);
        } else {
            // Invalid email or generation failed
            $upd = $pdo->prepare("UPDATE payroll_payslips SET email_status = 'failed' WHERE id = ?");
            $upd->execute([$payslip_id]);
        }
        $processed_count++;
    }
}

// Check how many are remaining
$stmt_rem = $pdo->prepare("
    SELECT COUNT(*) FROM payroll_payslips 
    WHERE batch_id = ? AND email_status = 'pending'
");
$stmt_rem->execute([$batch_id]);
$remaining = $stmt_rem->fetchColumn();

if ($remaining == 0) {
    // Done! Update batch status
    $stmt_upd = $pdo->prepare("UPDATE payroll_batches SET status = 'completed' WHERE id = ? AND status = 'emails_processing'");
    $stmt_upd->execute([$batch_id]);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Processing Emails...</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php if ($remaining > 0): ?>
        <meta http-equiv="refresh" content="2;url=payroll_email_processor.php?batch_id=<?php echo $batch_id; ?>">
    <?php endif; ?>
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-sm p-5 text-center" style="max-width: 500px;">
        <?php if ($remaining > 0): ?>
            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h4>Processing Emails...</h4>
            <p class="text-muted mb-0">Processed <?php echo $processed_count; ?> emails in this batch.</p>
            <p class="text-muted mb-0"><?php echo $remaining; ?> emails remaining in the queue.</p>
            <p class="text-warning small mt-2">Please do not close this window until complete.</p>
        <?php else: ?>
            <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
            <h4 class="mt-3">All Emails Processed!</h4>
            <p class="text-muted">The queue for this batch has been fully processed.</p>
            <a href="payroll_batches.php" class="btn btn-primary mt-3">Return to Batches</a>
            <!-- Using dashboard.php if payroll_batches.php doesn't exist yet -->
        <?php endif; ?>
    </div>
</body>
</html>
