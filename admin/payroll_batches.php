<?php
/**
 * List Payroll Batches
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/payroll_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $batch_id = (int)$_POST['batch_id'];
    
    try {
        $pdo->beginTransaction();
        
        $pdo->prepare("DELETE FROM payroll_payslip_items WHERE payslip_id IN (SELECT id FROM payroll_payslips WHERE batch_id = ?)")->execute([$batch_id]);
        $pdo->prepare("DELETE FROM payroll_payslips WHERE batch_id = ?")->execute([$batch_id]);
        $pdo->prepare("DELETE FROM payroll_batches WHERE id = ?")->execute([$batch_id]);
        
        $pdo->commit();
        set_flash_message('success', 'Batch successfully deleted.');
    } catch (Exception $e) {
        $pdo->rollBack();
        set_flash_message('danger', 'Failed to delete batch: ' . $e->getMessage());
    }
    header("Location: payroll_batches.php");
    exit;
}

// Fetch all batches
// Pagination logic
$limit = 15;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Fetch paginated batches
$stmt = $pdo->prepare("SELECT * FROM payroll_batches ORDER BY month DESC, created_at DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$batches = $stmt->fetchAll();

// Total count
$total_stmt = $pdo->query("SELECT COUNT(*) FROM payroll_batches");
$total_batches = $total_stmt->fetchColumn();
$total_pages = ceil($total_batches / $limit);
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
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <a href="payroll_batch_view.php?id=<?php echo $batch['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-eye"></i> View
                                                        </a>
                                                        <form method="POST" action="payroll_batches.php" onsubmit="return confirm('Are you sure you want to delete this entire batch? This cannot be undone.');">
                                                            <input type="hidden" name="action" value="delete">
                                                            <input type="hidden" name="batch_id" value="<?php echo $batch['id']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="bi bi-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                        <div class="d-flex justify-content-center p-3 border-top">
                            <nav aria-label="Page navigation">
                                <ul class="pagination mb-0">
                                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                                    </li>
                                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                        <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
