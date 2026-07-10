<?php
/**
 * Download Payroll XLSX Template
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/payroll_functions.php';
require_once __DIR__ . '/../includes/SimpleXLSXGen.php';

$components = get_payroll_components($pdo);

// Header row
$headers = ['Emp_Code', 'Employee_Name', 'Location', 'Designation', 'Bank_Name', 'Bank_Account', 'DOJ', 'PAN', 'PF_No', 'PF_UAN', 'ESIC_No', 'Work_Days', 'LOP_Days', 'Standard_Days', 'Prev_Month_LOP', 'LOP_Reversal'];
foreach ($components as $comp) {
    $headers[] = $comp['name']; // Use component name exactly as header
}

// Optional: Add a sample row
$sample = ['EMP001', 'ALKA JOSHI', 'RAIGARH', 'DIRECTOR', 'KOTAK', '7811474297', '11-11-2025', 'AUGPJ4887Q', 'CG/RAI/37194/28000', '102352231406', 'NA', '30', '0', '30', '0', '0'];
foreach ($components as $comp) {
    $sample[] = ''; // Leave blank to use employee's standard salary structure
}

$data = [
    $headers,
    $sample
];

$xlsx = Shuchkin\SimpleXLSXGen::fromArray($data);
$xlsx->downloadAs('payroll_upload_template.xlsx');
exit;
