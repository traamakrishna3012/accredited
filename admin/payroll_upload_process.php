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

$file_name = $_FILES['csv_file']['name'];
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
$tmp_name = $_FILES['csv_file']['tmp_name'];

$rows = [];

if ($file_ext === 'xlsx') {
    require_once __DIR__ . '/../includes/SimpleXLSX.php';
    if ($xlsx = Shuchkin\SimpleXLSX::parse($tmp_name)) {
        $rows = $xlsx->rows();
    } else {
        set_flash_message('danger', 'Error parsing XLSX file. It might be corrupted.');
        header("Location: payroll_upload.php");
        exit;
    }
} else {
    $file = fopen($tmp_name, 'r');
    if ($file !== false) {
        while (($row = fgetcsv($file)) !== false) {
            $rows[] = $row;
        }
        fclose($file);
    }
}

if (empty($rows)) {
    set_flash_message('danger', 'The uploaded file is empty or invalid.');
    header("Location: payroll_upload.php");
    exit;
}

$header = array_shift($rows);

if (!$header || !in_array('Emp_Code', $header) || !in_array('Work_Days', $header) || !in_array('LOP_Days', $header)) {
    set_flash_message('danger', 'Invalid file format. Please use the provided template or ensure Emp_Code, Work_Days, and LOP_Days columns exist.');
    header("Location: payroll_upload.php");
    exit;
}

$col_idx = array_flip($header);
$idx_emp = $col_idx['Emp_Code'];
$idx_work = $col_idx['Work_Days'];
$idx_lop = $col_idx['LOP_Days'];
$idx_std = $col_idx['Standard_Days'] ?? -1;
$idx_prev = $col_idx['Prev_Month_LOP'] ?? -1;
$idx_rev = $col_idx['LOP_Reversal'] ?? -1;

$idx_loc = $col_idx['Location'] ?? -1;
$idx_desig = $col_idx['Designation'] ?? -1;
$idx_bank = $col_idx['Bank_Name'] ?? -1;
$idx_acc = $col_idx['Bank_Account'] ?? -1;
$idx_doj = $col_idx['DOJ'] ?? -1;
$idx_pan = $col_idx['PAN'] ?? -1;
$idx_pf = $col_idx['PF_No'] ?? -1;
$idx_uan = $col_idx['PF_UAN'] ?? -1;
$idx_esic = $col_idx['ESIC_No'] ?? -1;

// Map header columns to component IDs
$components = get_payroll_components($pdo);
$comp_map = []; // name => id
$comp_details = []; // id => data
foreach ($components as $c) {
    $comp_map[$c['name']] = $c['id'];
    $comp_details[$c['id']] = $c;
}

$header_comps = [];
for ($i = 0; $i < count($header); $i++) {
    $h = trim($header[$i]);
    if (in_array($h, ['Emp_Code', 'Employee_Name', 'Location', 'Designation', 'Bank_Name', 'Bank_Account', 'DOJ', 'PAN', 'PF_No', 'PF_UAN', 'ESIC_No', 'Work_Days', 'LOP_Days', 'Standard_Days', 'Prev_Month_LOP', 'LOP_Reversal'])) {
        continue;
    }
    if (isset($comp_map[$h])) {
        $header_comps[$i] = $comp_map[$h];
    }
}

$employees_raw = get_payroll_employees($pdo);
$employees = [];
foreach ($employees_raw as $e) {
    $employees[trim($e['emp_code'])] = $e;
}

$errors = [];
$preview_data = [];
$row_num = 1;

foreach ($rows as $row) {
    $row_num++;
    if (empty(array_filter($row))) continue; // skip empty rows

    $raw_emp = $row[$idx_emp] ?? '';
    // Remove BOM and non-printable characters
    $emp_code = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', trim($raw_emp, " \t\n\r\0\x0B\xEF\xBB\xBF"));
    $emp_code = trim($emp_code);
    if (empty($emp_code)) continue;

    $matched_emp_code = null;
    if (isset($employees[$emp_code])) {
        $matched_emp_code = $emp_code;
    } else {
        // Try to match 'EMP001' from CSV to '0001' or '1' in DB
        if (preg_match('/^EMP0*(\d+)$/i', $emp_code, $matches)) {
            $normalized = str_pad($matches[1], 4, '0', STR_PAD_LEFT);
            if (isset($employees[$normalized])) {
                $matched_emp_code = $normalized;
            } elseif (isset($employees[$matches[1]])) {
                $matched_emp_code = $matches[1];
            }
        }
        // Try to match '0001' from CSV to 'EMP001' in DB
        if (!$matched_emp_code && preg_match('/^0*(\d+)$/', $emp_code, $matches)) {
            $normalized2 = 'EMP' . str_pad($matches[1], 3, '0', STR_PAD_LEFT);
            if (isset($employees[$normalized2])) {
                $matched_emp_code = $normalized2;
            }
        }
    }

    if (!$matched_emp_code) {
        $errors[] = "Row $row_num: Employee code '$emp_code' not found.";
        continue;
    }

    $emp = $employees[$matched_emp_code];
    if (strtolower(trim($emp['status'])) !== 'active') {
        $errors[] = "Row $row_num: Employee '$emp_code' is inactive.";
        continue;
    }
    
    // Update emp_code to the matched DB code so the preview data maps correctly
    $emp_code = $matched_emp_code;
    $work_days = (float)($row[$idx_work] ?? 0);
    $lop_days = (float)($row[$idx_lop] ?? 0);

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
        'standard_days' => ($idx_std >= 0 && isset($row[$idx_std]) && is_numeric(trim($row[$idx_std]))) ? (float)trim($row[$idx_std]) : 0,
        'prev_month_lop' => ($idx_prev >= 0 && isset($row[$idx_prev]) && is_numeric(trim($row[$idx_prev]))) ? (float)trim($row[$idx_prev]) : 0,
        'lop_reversal' => ($idx_rev >= 0 && isset($row[$idx_rev]) && is_numeric(trim($row[$idx_rev]))) ? (float)trim($row[$idx_rev]) : 0,
        'metadata' => [
            'location' => $idx_loc >= 0 ? trim($row[$idx_loc] ?? '') : '',
            'designation' => $idx_desig >= 0 ? trim($row[$idx_desig] ?? '') : '',
            'bank_name' => $idx_bank >= 0 ? trim($row[$idx_bank] ?? '') : '',
            'bank_account' => $idx_acc >= 0 ? trim($row[$idx_acc] ?? '') : '',
            'doj' => $idx_doj >= 0 ? trim($row[$idx_doj] ?? '') : '',
            'pan_no' => $idx_pan >= 0 ? trim($row[$idx_pan] ?? '') : '',
            'pf_no' => $idx_pf >= 0 ? trim($row[$idx_pf] ?? '') : '',
            'uan_no' => $idx_uan >= 0 ? trim($row[$idx_uan] ?? '') : '',
            'esic_no' => $idx_esic >= 0 ? trim($row[$idx_esic] ?? '') : '',
        ],
        'components' => $calc_struct,
        'earnings' => $calc_results['total_earnings'],
        'deductions' => $calc_results['total_deductions'],
        'net_pay' => $calc_results['net_pay'],
        'items' => $calc_results['items']
    ];
}

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

