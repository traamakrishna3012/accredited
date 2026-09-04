<?php
/**
 * CLI & Web Spam Cleanup Utility
 * Accredited Inspection Agency
 * 
 * Usage from CLI: php admin/clean_spam.php
 * Usage from Web: Must be logged in as Admin, then visit https://accredited.co.in/admin/clean_spam.php
 */

$is_cli = (php_sapi_name() === 'cli');

if (!$is_cli) {
    require_once __DIR__ . '/auth_check.php';
} else {
    require_once __DIR__ . '/../includes/db.php';
}

// Perform Cleanup
$before_count = get_total_inquiries($pdo);
$spam_count = count_spam_inquiries($pdo);

$action = $is_cli ? 'run' : ($_GET['run'] ?? '');

$deleted = 0;
if ($action === 'run' || $is_cli) {
    $deleted = delete_spam_inquiries($pdo);
    $after_count = get_total_inquiries($pdo);
} else {
    $after_count = $before_count;
}

// Find sample genuine inquiries remaining
$genuine_stmt = $pdo->query("SELECT id, name, email, phone, message, created_at FROM inquiries ORDER BY id DESC LIMIT 10");
$genuine_samples = $genuine_stmt->fetchAll();

if ($is_cli) {
    echo "========================================\n";
    echo " Accredited Anti-Spam Cleanup Utility\n";
    echo "========================================\n";
    echo "Total inquiries before: {$before_count}\n";
    echo "Spam detected:          {$spam_count}\n";
    echo "Spam records deleted:   {$deleted}\n";
    echo "Inquiries remaining:    {$after_count}\n";
    echo "========================================\n";
    if (!empty($genuine_samples)) {
        echo "Recent Genuine Inquiries:\n";
        foreach ($genuine_samples as $item) {
            echo "- [#{$item['id']}] {$item['name']} ({$item['email']}) - " . substr($item['message'], 0, 50) . "...\n";
        }
    } else {
        echo "No remaining inquiries in database.\n";
    }
    echo "Done!\n";
    exit(0);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/admin_head.php'; ?>
    <title>Spam Cleanup Utility | Admin Panel</title>
</head>
<body class="bg-light">
    <div class="d-flex">
        <?php include __DIR__ . '/admin_sidebar.php'; ?>

        <div class="flex-grow-1" style="max-width: calc(100vw - 250px);">
            <div class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Spam Cleanup Utility</h5>
                <a href="inquiries.php" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to Inquiries
                </a>
            </div>

            <div class="p-4">
                <?php if ($action === 'run'): ?>
                    <div class="alert alert-success d-flex align-items-center mb-4">
                        <i class="bi bi-check-circle-fill fs-2 me-3"></i>
                        <div>
                            <h5 class="alert-heading mb-1">Spam Cleanup Completed!</h5>
                            <p class="mb-0">
                                Successfully purged <strong><?php echo number_format($deleted); ?></strong> spam bot inquiries. 
                                <strong><?php echo number_format($after_count); ?></strong> genuine customer inquiries remain in your database.
                            </p>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">Database Status</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 text-center">
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded">
                                    <h3 class="text-primary mb-1"><?php echo number_format($before_count); ?></h3>
                                    <span class="text-muted small">Total Inquiries in Database</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded">
                                    <h3 class="text-danger mb-1"><?php echo number_format($spam_count); ?></h3>
                                    <span class="text-muted small">Identified Spam Bot Records</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded">
                                    <h3 class="text-success mb-1"><?php echo number_format($after_count); ?></h3>
                                    <span class="text-muted small">Clean Customer Inquiries</span>
                                </div>
                            </div>
                        </div>

                        <?php if ($spam_count > 0 && $action !== 'run'): ?>
                            <div class="mt-4 text-center">
                                <a href="clean_spam.php?run=run" 
                                   class="btn btn-danger btn-lg"
                                   onclick="return confirm('Purge <?php echo number_format($spam_count); ?> spam inquiries now?');">
                                    <i class="bi bi-trash3-fill me-2"></i>Run Spam Purge Now
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="mt-4 text-center">
                                <a href="inquiries.php" class="btn btn-primary">
                                    <i class="bi bi-envelope-check me-2"></i>View Clean Inquiries
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($genuine_samples)): ?>
                    <div class="card">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">Remaining Customer Inquiries (Preview)</h6>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($genuine_samples as $item): ?>
                                        <tr>
                                            <td><?php echo $item['id']; ?></td>
                                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                                            <td><?php echo htmlspecialchars($item['email']); ?></td>
                                            <td><?php echo htmlspecialchars($item['phone'] ?: '-'); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($item['created_at'])); ?></td>
                                            <td>
                                                <a href="inquiries.php?view=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye me-1"></i>View & Reply
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
