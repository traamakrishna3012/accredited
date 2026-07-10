<?php
/**
 * Add / Edit Payroll Employee
 * Accredited Inspection Agency
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/payroll_functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$employee = null;
$structure = [];

if ($id > 0) {
    $employee = get_payroll_employee($pdo, $id);
    if (!$employee) {
        set_flash_message('danger', 'Employee not found.');
        header("Location: payroll_employees.php");
        exit;
    }
}

$components = get_payroll_components($pdo);
// If editing, merge saved amounts, otherwise standard amounts are 0
if ($employee) {
    $saved_structure = get_employee_salary_structure($pdo, $id);
    foreach ($saved_structure as $comp) {
        $structure[$comp['id']] = $comp['standard_amount'] ?? 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Invalid CSRF token");
    }

    $emp_data = [
        'id' => $id,
        'emp_code' => sanitize_input($_POST['emp_code'] ?? ''),
        'name' => sanitize_input($_POST['name'] ?? ''),
        'designation' => sanitize_input($_POST['designation'] ?? ''),
        'location' => sanitize_input($_POST['location'] ?? ''),
        'doj' => !empty($_POST['doj']) ? $_POST['doj'] : null,
        'bank_name' => sanitize_input($_POST['bank_name'] ?? ''),
        'bank_account' => sanitize_input($_POST['bank_account'] ?? ''),
        'pan_no' => sanitize_input($_POST['pan_no'] ?? ''),
        'pf_no' => sanitize_input($_POST['pf_no'] ?? ''),
        'uan_no' => sanitize_input($_POST['uan_no'] ?? ''),
        'esic_no' => sanitize_input($_POST['esic_no'] ?? ''),
        'email' => sanitize_input($_POST['email'] ?? ''),
        'status' => sanitize_input($_POST['status'] ?? 'active'),
        'standard_days' => isset($_POST['standard_days']) && is_numeric($_POST['standard_days']) ? (float)$_POST['standard_days'] : 0,
        'prev_month_lop' => isset($_POST['prev_month_lop']) && is_numeric($_POST['prev_month_lop']) ? (float)$_POST['prev_month_lop'] : 0,
        'lop_reversal' => isset($_POST['lop_reversal']) && is_numeric($_POST['lop_reversal']) ? (float)$_POST['lop_reversal'] : 0,
    ];

    $comp_data = $_POST['components'] ?? [];

    try {
        save_payroll_employee($pdo, $emp_data, $comp_data);
        set_flash_message('success', 'Employee saved successfully.');
        header("Location: payroll_employees.php");
        exit;
    } catch (Exception $e) {
        $error = "Failed to save: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/admin_head.php'; ?>
    <title><?php echo $id ? 'Edit' : 'Add'; ?> Employee | Payroll</title>
</head>
<body class="bg-light">
    <div class="d-flex">
        <?php include __DIR__ . '/admin_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <div class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <a href="payroll_employees.php" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> Employees</a> 
                    / <?php echo $id ? 'Edit' : 'Add New'; ?> Employee
                </h5>
            </div>

            <div class="p-4">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <!-- Basic Info -->
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-white py-3">
                                    <h6 class="mb-0 fw-bold">Personal & Employment Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Employee Code *</label>
                                            <input type="text" name="emp_code" class="form-control" required value="<?php echo htmlspecialchars($employee['emp_code'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Full Name *</label>
                                            <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($employee['name'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Designation *</label>
                                            <input type="text" name="designation" class="form-control" required value="<?php echo htmlspecialchars($employee['designation'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($employee['email'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Location / Department</label>
                                            <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($employee['location'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Date of Joining</label>
                                            <input type="date" name="doj" class="form-control" value="<?php echo htmlspecialchars($employee['doj'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="active" <?php echo ($employee['status'] ?? '') == 'active' ? 'selected' : ''; ?>>Active</option>
                                                <option value="inactive" <?php echo ($employee['status'] ?? '') == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Statutory & Bank -->
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-white py-3">
                                    <h6 class="mb-0 fw-bold">Statutory & Bank Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">PAN Number</label>
                                            <input type="text" name="pan_no" class="form-control" value="<?php echo htmlspecialchars($employee['pan_no'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">PF Number</label>
                                            <input type="text" name="pf_no" class="form-control" value="<?php echo htmlspecialchars($employee['pf_no'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">UAN Number</label>
                                            <input type="text" name="uan_no" class="form-control" value="<?php echo htmlspecialchars($employee['uan_no'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">ESIC Number</label>
                                            <input type="text" name="esic_no" class="form-control" value="<?php echo htmlspecialchars($employee['esic_no'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Bank Name</label>
                                            <input type="text" name="bank_name" class="form-control" value="<?php echo htmlspecialchars($employee['bank_name'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Bank Account Number</label>
                                            <input type="text" name="bank_account" class="form-control" value="<?php echo htmlspecialchars($employee['bank_account'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Monthly Default Details -->
                            <div class="card shadow-sm border-0 mt-4">
                                <div class="card-header bg-white py-3">
                                    <h6 class="mb-0 fw-bold">Monthly Defaults (Leaves & Standard Days)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Default Standard Days</label>
                                            <input type="number" step="0.5" name="standard_days" class="form-control" value="<?php echo htmlspecialchars($employee['standard_days'] ?? '0'); ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Default Prev Month LOP</label>
                                            <input type="number" step="0.5" name="prev_month_lop" class="form-control" value="<?php echo htmlspecialchars($employee['prev_month_lop'] ?? '0'); ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Default LOP Reversal</label>
                                            <input type="number" step="0.5" name="lop_reversal" class="form-control" value="<?php echo htmlspecialchars($employee['lop_reversal'] ?? '0'); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <!-- Salary Structure -->
                            <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                                <div class="card-header bg-white py-3">
                                    <h6 class="mb-0 fw-bold">Standard Salary Structure</h6>
                                    <small class="text-muted">Set monthly standard entitlement (₹)</small>
                                </div>
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        <?php 
                                        $current_type = '';
                                        foreach ($components as $comp): 
                                            if ($current_type !== $comp['type']) {
                                                $current_type = $comp['type'];
                                                $bg = $current_type == 'earning' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger';
                                                echo '<li class="list-group-item py-2 fw-semibold ' . $bg . '">' . ucfirst($current_type) . 's</li>';
                                            }
                                            $val = $structure[$comp['id']] ?? 0;
                                        ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <?php echo htmlspecialchars($comp['name']); ?>
                                                <?php if($comp['is_prorated']): ?>
                                                    <span class="badge bg-light text-secondary ms-1 border" title="Reduces on LOP">Prorated</span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-secondary ms-1 border" title="Fixed regardless of LOP">Fixed</span>
                                                <?php endif; ?>
                                            </div>
                                            <div style="width: 120px;">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">₹</span>
                                                    <input type="number" step="0.01" name="components[<?php echo $comp['id']; ?>]" class="form-control text-end component-input" data-type="<?php echo $comp['type']; ?>" value="<?php echo number_format((float)$val, 2, '.', ''); ?>">
                                                </div>
                                            </div>
                                        </li>
                                        <?php endforeach; ?>
                                        <li class="list-group-item bg-light">
                                            <div class="d-flex justify-content-between fw-bold">
                                                <span>Standard Gross Pay:</span>
                                                <span id="gross_pay">₹0.00</span>
                                            </div>
                                            <div class="d-flex justify-content-between text-muted small mt-1">
                                                <span>Standard Deductions:</span>
                                                <span id="total_deductions">₹0.00</span>
                                            </div>
                                            <div class="d-flex justify-content-between fw-bold text-primary mt-2 pt-2 border-top">
                                                <span>Standard Net Pay:</span>
                                                <span id="net_pay">₹0.00</span>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-footer bg-white py-3">
                                    <button type="submit" class="btn btn-primary w-100 mb-2">Save Employee</button>
                                    <a href="payroll_employees.php" class="btn btn-outline-secondary w-100">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.component-input');
            const grossEl = document.getElementById('gross_pay');
            const dedEl = document.getElementById('total_deductions');
            const netEl = document.getElementById('net_pay');

            function calculateTotals() {
                let gross = 0;
                let deductions = 0;

                inputs.forEach(input => {
                    const val = parseFloat(input.value) || 0;
                    if (input.dataset.type === 'earning') {
                        gross += val;
                    } else if (input.dataset.type === 'deduction') {
                        deductions += val;
                    }
                });

                grossEl.textContent = '₹' + gross.toFixed(2);
                dedEl.textContent = '₹' + deductions.toFixed(2);
                netEl.textContent = '₹' + (gross - deductions).toFixed(2);
            }

            inputs.forEach(input => {
                input.addEventListener('input', calculateTotals);
            });

            calculateTotals();
        });
    </script>
</body>
</html>
