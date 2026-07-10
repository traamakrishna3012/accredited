<?php
/**
 * List Payroll Batches
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/payroll_functions.php';

// Fetch all batches
$stmt = $pdo->query("SELECT * FROM payroll_batches ORDER BY month DESC, created_at DESC");
$batches = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/admin_head.php'; ?>
    <title>Payroll Batches | Admin</title>
</head>
<body class="bg-light">
    <div class="d-flex">
        <?php include __DIR__ . '/admin_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <div class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Payroll Batches</h5>
                <a href="payroll_upload.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle"></i> New Batch</a>
            </div>

            <div class="p-4">
                <?php if ($flash = get_flash_message()): ?>
                    <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show">
                        <?php echo htmlspecialchars($flash['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Month</th>
                                        <th>Status</th>
                                        <th>Standard Days</th>
                                        <th>Created At</th>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($batches)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">No payroll batches found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($batches as $batch): ?>
                                            <tr>
                                                <td class="ps-4 fw-bold">
                                                    <?php echo date('F Y', strtotime($batch['month'] . '-01')); ?>
                                                </td>
                                                <td>
                                                    <?php if ($batch['status'] === 'draft'): ?>
                                                        <span class="badge bg-secondary">Draft</span>
                                                    <?php elseif ($batch['status'] === 'emails_processing'): ?>
                                                        <span class="badge bg-warning text-dark">Sending Emails...</span>
                                                    <?php elseif ($batch['status'] === 'completed'): ?>
                                                        <span class="badge bg-success">Completed</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-primary"><?php echo htmlspecialchars($batch['status']); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo $batch['standard_working_days']; ?></td>
                                                <td><?php echo date('d M Y, h:i A', strtotime($batch['created_at'])); ?></td>
                                                <td class="text-end pe-4">
                                                    <a href="payroll_batch_view.php?id=<?php echo $batch['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i> View Details
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
