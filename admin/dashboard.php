<?php
/**
 * Admin Dashboard
 * Accredited Inspection Agency
 */

require_once __DIR__ . '/auth_check.php';

// Get dashboard stats
$inquiry_counts = get_inquiry_counts($pdo);
$recent_inquiries = get_inquiries($pdo, 5);

// Count services
$stmt = $pdo->query("SELECT COUNT(*) as count FROM services");
$services_count = $stmt->fetch()['count'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Dashboard | Admin Panel</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?php echo BASE_URL; ?>/assets/images/Agency_Logo.svg">
    <link rel="alternate icon" type="image/x-icon" href="<?php echo BASE_URL; ?>/assets/images/favicon.ico">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/assets/images/favicon.ico">

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
                    <a class="nav-link <?php echo $admin_page == 'dashboard' ? 'active' : ''; ?>" href="dashboard.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $admin_page == 'inquiries' ? 'active' : ''; ?>" href="inquiries.php">
                        <i class="bi bi-envelope"></i> Inquiries
                        <?php if ($inquiry_counts['new_count'] > 0): ?>
                            <span class="badge bg-danger ms-auto"><?php echo $inquiry_counts['new_count']; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $admin_page == 'services' ? 'active' : ''; ?>" href="services.php">
                        <i class="bi bi-grid"></i> Services
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $admin_page == 'jobs' ? 'active' : ''; ?>" href="jobs.php">
                        <i class="bi bi-briefcase"></i> Jobs
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $admin_page == 'settings' ? 'active' : ''; ?>" href="settings.php">
                        <i class="bi bi-gear"></i> Settings
                    </a>
                </li>
            </ul>

            <hr class="border-secondary my-4">
            
            <div class="text-white mb-2 px-3">
                <small class="opacity-75 text-uppercase fw-bold" style="font-size: 0.75rem;">Payroll</small>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="payroll_batches.php">
                        <i class="bi bi-files"></i> Batches
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="payroll_upload.php">
                        <i class="bi bi-cloud-upload"></i> Bulk Upload
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="payroll_employees.php">
                        <i class="bi bi-people"></i> Employees
                    </a>
                </li>
            </ul>

            <hr class="border-secondary my-4">

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/" target="_blank">
                        <i class="bi bi-globe"></i> View Website
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <i class="bi bi-box-arrow-left"></i> Logout
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <!-- Top Bar -->
            <div class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Dashboard</h5>
                <div>
                    <span class="text-muted me-2">Welcome,</span>
                    <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong>
                </div>
            </div>

            <!-- Content -->
            <div class="p-4">
                <!-- Stats Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0 text-white"><?php echo $inquiry_counts['total'] ?? 0; ?></h3>
                                    <span class="opacity-75">Total Inquiries</span>
                                </div>
                                <i class="bi bi-envelope fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card bg-warning">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0"><?php echo $inquiry_counts['new_count'] ?? 0; ?></h3>
                                    <span class="opacity-75">New Inquiries</span>
                                </div>
                                <i class="bi bi-bell fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card bg-success">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0"><?php echo $inquiry_counts['responded_count'] ?? 0; ?></h3>
                                    <span class="opacity-75">Responded</span>
                                </div>
                                <i class="bi bi-check-circle fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card bg-info">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0"><?php echo $services_count; ?></h3>
                                    <span class="opacity-75">Services</span>
                                </div>
                                <i class="bi bi-grid fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Inquiries -->
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Recent Inquiries</h6>
                        <a href="inquiries.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Service</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recent_inquiries)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">No inquiries yet.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recent_inquiries as $inquiry): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($inquiry['name']); ?></td>
                                                <td><?php echo htmlspecialchars($inquiry['email']); ?></td>
                                                <td><?php echo $inquiry['service_name'] ? htmlspecialchars($inquiry['service_name']) : '<span class="text-muted">General</span>'; ?>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($inquiry['created_at'])); ?></td>
                                                <td>
                                                    <?php
                                                    $status_class = [
                                                        'new' => 'bg-warning',
                                                        'read' => 'bg-info',
                                                        'responded' => 'bg-success'
                                                    ];
                                                    ?>
                                                    <span
                                                        class="badge <?php echo $status_class[$inquiry['status']] ?? 'bg-secondary'; ?>">
                                                        <?php echo ucfirst($inquiry['status']); ?>
                                                    </span>
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