<?php
/**
 * Payroll Bulk Upload
 * Accredited Inspection Agency
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/payroll_functions.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Upload Payroll | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="admin-sidebar" style="width: 250px;">
            <div class="text-white mb-4">
                <h5 class="mb-0 text-white">Admin Panel</h5>
                <small class="opacity-75"><?php echo SITE_NAME; ?></small>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="payroll_employees.php"><i class="bi bi-people"></i> Employees (Payroll)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="payroll_upload.php"><i class="bi bi-cloud-upload"></i> Bulk Upload</a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <div class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Payroll - Bulk Upload</h5>
            </div>

            <div class="p-4">
                <?php if ($flash = get_flash_message()): ?>
                    <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show">
                        <?php echo htmlspecialchars($flash['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-body p-4">
                                <h5 class="card-title fw-bold mb-3"><i class="bi bi-1-circle text-primary me-2"></i>Step 1: Download Template</h5>
                                <p class="text-muted">Download the dynamic CSV template. It automatically includes columns for all active salary components.</p>
                                <a href="payroll_download_template.php" class="btn btn-outline-primary">
                                    <i class="bi bi-download me-2"></i>Download CSV Template
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0">
                            <div class="card-body p-4">
                                <h5 class="card-title fw-bold mb-3"><i class="bi bi-2-circle text-primary me-2"></i>Step 2: Upload Data</h5>
                                <p class="text-muted">Fill the template with employee data for a specific month. Leave component columns blank to use their standard fixed salary.</p>
                                
                                <form action="payroll_upload_process.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Select Payroll Month</label>
                                        <input type="month" name="payroll_month" class="form-control" required value="<?php echo date('Y-m'); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Standard Working Days (for this month)</label>
                                        <input type="number" name="standard_days" class="form-control" step="0.5" required value="30">
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Upload CSV File</label>
                                        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-upload me-2"></i>Upload and Preview
                                    </button>
                                </form>
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
