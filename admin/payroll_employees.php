<?php
/**
 * Payroll Employees List
 * Accredited Inspection Agency
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/payroll_functions.php';

// Pagination logic
$limit = 15;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Fetch paginated employees
$stmt = $pdo->prepare("SELECT * FROM payroll_employees ORDER BY emp_code ASC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$employees = $stmt->fetchAll();

// Total count
$total_stmt = $pdo->query("SELECT COUNT(*) FROM payroll_employees");
$total_employees = $total_stmt->fetchColumn();
$total_pages = ceil($total_employees / $limit);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include __DIR__ . '/admin_head.php'; ?>
    <title>Employees Master | Payroll</title>
</head>

<body class="bg-light">
    <div class="d-flex">
        <?php include __DIR__ . '/admin_sidebar.php'; ?>

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
