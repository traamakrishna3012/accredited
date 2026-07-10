<?php
/**
 * Process Payroll Bulk Upload and Show Preview
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/payroll_functions.php';
require_once __DIR__ . '/../includes/payroll_calculator.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? '')) {
    die("Invalid request");
}

$month = $_POST['payroll_month'] ?? '';
$standard_days = (float)($_POST['standard_days'] ?? 30);

if (empty($_FILES['csv_file']['tmp_name'])) {
    set_flash_message('danger', 'Please upload a file.');
    header("Location: payroll_upload.php");
    exit;
}

$file = fopen($_FILES['csv_file']['tmp_name'], 'r');
$header = fgetcsv($file);

if (!$header || $header[0] !== 'Emp_Code' || $header[1] !== 'Work_Days' || $header[2] !== 'LOP_Days') {
    set_flash_message('danger', 'Invalid CSV format. Please use the provided template.');
    header("Location: payroll_upload.php");
    exit;
}

// Map header columns to component IDs
$components = get_payroll_components($pdo);
$comp_map = []; // name => id
$comp_details = []; // id => data
foreach ($components as $c) {
    $comp_map[$c['name']] = $c['id'];
    $comp_details[$c['id']] = $c;
}

$header_comps = [];
for ($i = 3; $i < count($header); $i++) {
    $h = trim($header[$i]);
    if (isset($comp_map[$h])) {
        $header_comps[$i] = $comp_map[$h];
    }
}

$employees_raw = get_payroll_employees($pdo, 'active');
$employees = [];
foreach ($employees_raw as $e) {
    $employees[$e['emp_code']] = $e;
}

$errors = [];
$preview_data = [];
$row_num = 1;

while (($row = fgetcsv($file)) !== false) {
    $row_num++;
    if (empty(array_filter($row))) continue; // skip empty rows

    $emp_code = trim($row[0] ?? '');
    if (empty($emp_code)) continue;

    if (!isset($employees[$emp_code])) {
        $errors[] = "Row $row_num: Employee code '$emp_code' not found or inactive.";
        continue;
    }

    $emp = $employees[$emp_code];
    $work_days = (float)($row[1] ?? 0);
    $lop_days = (float)($row[2] ?? 0);

    if ($work_days + $lop_days > $standard_days) {
        $errors[] = "Row $row_num: Work Days + LOP Days exceeds Standard Days ($standard_days) for $emp_code.";
    }

    // Get standard structure to fallback on
    $std_struct_raw = get_employee_salary_structure($pdo, $emp['id']);
    $emp_structure = [];
    foreach ($std_struct_raw as $ss) {
        $emp_structure[$ss['id']] = $ss['standard_amount'] ?? 0;
    }

    // Build the calculation structure array
    $calc_struct = [];
    foreach ($components as $c) {
        $cid = $c['id'];
        $val = $emp_structure[$cid] ?? 0; // standard fallback
        
        // Find if it was provided in CSV
        foreach ($header_comps as $col_idx => $header_cid) {
            if ($header_cid == $cid && isset($row[$col_idx]) && trim($row[$col_idx]) !== '') {
                $val = (float)trim($row[$col_idx]);
                break;
            }
        }
        
        $calc_struct[] = [
            'component_id' => $cid,
            'type' => $c['type'],
            'is_prorated' => $c['is_prorated'],
            'standard_amount' => $val // We use the overridden val as the standard for this month
        ];
    }

    $calc_results = calculate_payslip_totals($calc_struct, $standard_days, $work_days);
    
    $preview_data[] = [
        'employee_id' => $emp['id'],
        'emp_code' => $emp_code,
        'name' => $emp['name'],
        'work_days' => $work_days,
        'lop_days' => $lop_days,
        'earnings' => $calc_results['total_earnings'],
        'deductions' => $calc_results['total_deductions'],
        'net_pay' => $calc_results['net_pay'],
        'items' => $calc_results['items']
    ];
}
fclose($file);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include __DIR__ . '/admin_head.php'; ?>
    <title>Preview Upload | Admin</title>
</head>
<body class="bg-light">
    <div class="d-flex">
        <?php include __DIR__ . '/admin_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <div class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Upload Preview (<?php echo htmlspecialchars($month); ?>)</h5>
                <a href="payroll_upload.php" class="btn btn-outline-secondary btn-sm">Cancel</a>
            </div>

            <div class="p-4">

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <strong>Please fix these errors in your CSV and try again:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <a href="payroll_upload.php" class="btn btn-primary">Go Back</a>
        <?php else: ?>
            <div class="alert alert-info">
                <strong>Standard Days:</strong> <?php echo $standard_days; ?><br>
                Please review the calculated totals before committing. If everything looks good, click Commit to generate payslips.
            </div>

            <form action="payroll_upload_commit.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <input type="hidden" name="month" value="<?php echo htmlspecialchars($month); ?>">
                <input type="hidden" name="standard_days" value="<?php echo $standard_days; ?>">
                
                <!-- We serialize the validated payload so we don't have to re-upload. -->
                <!-- In a real app with 10k rows we might save to a temp table, but this is fine for typical SMB payrolls -->
                <textarea name="payload_json" style="display:none;"><?php echo htmlspecialchars(json_encode($preview_data)); ?></textarea>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Emp Code</th>
                                <th>Name</th>
                                <th>Work Days</th>
                                <th>LOP Days</th>
                                <th>Earnings (₹)</th>
                                <th>Deductions (₹)</th>
                                <th>Net Pay (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($preview_data as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['emp_code']); ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo $row['work_days']; ?></td>
                                    <td><?php echo $row['lop_days']; ?></td>
                                    <td class="text-success"><?php echo number_format($row['earnings'], 2); ?></td>
                                    <td class="text-danger"><?php echo number_format($row['deductions'], 2); ?></td>
                                    <td class="fw-bold"><?php echo number_format($row['net_pay'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="btn btn-success btn-lg">
                    <i class="bi bi-check-circle me-2"></i> Commit Payroll for <?php echo count($preview_data); ?> Employees
                </button>
            </form>
        <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

