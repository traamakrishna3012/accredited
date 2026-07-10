<?php
/**
 * Payroll Employees List
 * Accredited Inspection Agency
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/payroll_functions.php';

$employees = get_payroll_employees($pdo);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Employees Master | Payroll</title>
    
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
                    <a class="nav-link" href="dashboard.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="payroll_employees.php">
                        <i class="bi bi-people"></i> Employees (Payroll)
                    </a>
                </li>
                <!-- We will add other links later like Batches -->
                <li class="nav-item">
                    <a class="nav-link" href="services.php">
                        <i class="bi bi-grid"></i> Services
                    </a>
                </li>
            </ul>
            <hr class="border-secondary my-4">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <i class="bi bi-box-arrow-left"></i> Logout
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <div class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Payroll - Employee Master</h5>
                <div>
                    <a href="payroll_employee_edit.php" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg"></i> Add New Employee
                    </a>
                </div>
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
                                        <th class="ps-4">Emp Code</th>
                                        <th>Name</th>
                                        <th>Designation</th>
                                        <th>Location</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($employees)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                No employees found. Click "Add New Employee" to start.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($employees as $emp): ?>
                                            <tr>
                                                <td class="ps-4 fw-medium"><?php echo htmlspecialchars($emp['emp_code']); ?></td>
                                                <td>
                                                    <div class="fw-medium"><?php echo htmlspecialchars($emp['name']); ?></div>
                                                </td>
                                                <td><?php echo htmlspecialchars($emp['designation']); ?></td>
                                                <td><?php echo htmlspecialchars($emp['location']); ?></td>
                                                <td><?php echo htmlspecialchars($emp['email']); ?></td>
                                                <td>
                                                    <?php if ($emp['status'] == 'active'): ?>
                                                        <span class="badge bg-success-subtle text-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <a href="payroll_employee_edit.php?id=<?php echo $emp['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-pencil"></i> Edit
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
