<?php
/**
 * View Single Payroll Batch
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/payroll_functions.php';

$batch_id = $_GET['id'] ?? null;
if (!$batch_id) {
    die("Batch ID is required.");
}

// Fetch Batch
$stmt = $pdo->prepare("SELECT * FROM payroll_batches WHERE id = ?");
$stmt->execute([$batch_id]);
$batch = $stmt->fetch();
if (!$batch) {
    die("Batch not found.");
}

// Fetch Payslips
$stmt = $pdo->prepare("
    SELECT p.*, e.name, e.emp_code, e.email 
    FROM payroll_payslips p
    JOIN payroll_employees e ON p.employee_id = e.id
    WHERE p.batch_id = ?
    ORDER BY e.name ASC
");
$stmt->execute([$batch_id]);
$payslips = $stmt->fetchAll();

// Fetch Logs
$stmt_logs = $pdo->prepare("
    SELECT * FROM payroll_upload_logs 
    WHERE batch_id = ? 
    ORDER BY created_at DESC
");
$stmt_logs->execute([$batch_id]);
$logs = $stmt_logs->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/admin_head.php'; ?>
    <title>Batch Details | Admin</title>
</head>
<body class="bg-light">
    <div class="d-flex">
        <?php include __DIR__ . '/admin_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <div class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <a href="payroll_batches.php" class="btn btn-sm btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i> Back</a>
                    <h5 class="mb-0">Batch Details: <?php echo date('F Y', strtotime($batch['month'] . '-01')); ?></h5>
                </div>
                <div>
                    <a href="payroll_batch_zip.php?batch_id=<?php echo $batch['id']; ?>" class="btn btn-outline-primary btn-sm me-2">
                        <i class="bi bi-file-earmark-zip"></i> Download All PDFs (ZIP)
                    </a>
                    
                    <form action="payroll_batch_email_trigger.php" method="POST" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <input type="hidden" name="batch_id" value="<?php echo $batch['id']; ?>">
                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('This will queue all payslips to be sent via email. Continue?');">
                            <i class="bi bi-envelope"></i> Send All Emails
                        </button>
                    </form>
                </div>
            </div>

            <div class="p-4">
                <?php if ($flash = get_flash_message()): ?>
                    <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show">
                        <?php echo htmlspecialchars($flash['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-9">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="mb-0 fw-bold">Payslips (<?php echo count($payslips); ?>)</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-4">Emp Code</th>
                                                <th>Name</th>
                                                <th>Net Pay</th>
                                                <th>Email Status</th>
                                                <th class="text-end pe-4">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($payslips as $ps): ?>
                                                <tr>
                                                    <td class="ps-4"><?php echo htmlspecialchars($ps['emp_code']); ?></td>
                                                    <td>
                                                        <?php echo htmlspecialchars($ps['name']); ?>
                                                        <?php if (empty($ps['email'])): ?>
                                                            <small class="text-danger d-block"><i class="bi bi-exclamation-triangle"></i> No email</small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="fw-bold text-success">₹<?php echo number_format($ps['net_pay'], 2); ?></td>
                                                    <td>
                                                        <?php if ($ps['email_status'] == 'sent'): ?>
                                                            <span class="badge bg-success"><i class="bi bi-check"></i> Sent</span>
                                                        <?php elseif ($ps['email_status'] == 'failed'): ?>
                                                            <span class="badge bg-danger"><i class="bi bi-x"></i> Failed</span>
                                                        <?php elseif ($ps['email_status'] == 'pending'): ?>
                                                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass"></i> Pending</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Unsent</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-end pe-4">
                                                        <a href="payroll_payslip_pdf.php?id=<?php echo $ps['id']; ?>&action=view" target="_blank" class="btn btn-sm btn-outline-secondary" title="View PDF">
                                                            <i class="bi bi-file-pdf"></i>
                                                        </a>
                                                        <a href="payroll_payslip_pdf.php?id=<?php echo $ps['id']; ?>" class="btn btn-sm btn-outline-primary" title="Download PDF">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="mb-0 fw-bold">Batch Logs</h6>
                            </div>
                            <div class="card-body p-3">
                                <?php if (empty($logs)): ?>
                                    <p class="text-muted small">No logs found.</p>
                                <?php else: ?>
                                    <ul class="list-unstyled mb-0 small">
                                        <?php foreach ($logs as $log): ?>
                                            <li class="mb-3 border-bottom pb-2">
                                                <div class="text-muted" style="font-size: 0.75rem;">
                                                    <i class="bi bi-clock"></i> <?php echo date('d M Y, h:i A', strtotime($log['created_at'])); ?>
                                                </div>
                                                <div><?php echo htmlspecialchars($log['log_message']); ?></div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
