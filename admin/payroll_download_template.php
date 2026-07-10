<?php
/**
 * Download Payroll XLSX Template
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/payroll_functions.php';
require_once __DIR__ . '/../includes/SimpleXLSXGen.php';

$components = get_payroll_components($pdo);

// Header row
$headers = ['Emp_Code', 'Work_Days', 'LOP_Days'];
foreach ($components as $comp) {
    $headers[] = $comp['name']; // Use component name exactly as header
}

// Optional: Add a sample row
$sample = ['EMP001', '30', '0'];
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
