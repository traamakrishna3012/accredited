<?php
/**
 * Download Payroll CSV Template
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/payroll_functions.php';

$components = get_payroll_components($pdo);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=payroll_upload_template.csv');

$output = fopen('php://output', 'w');

// Header row
$headers = ['Emp_Code', 'Work_Days', 'LOP_Days'];
foreach ($components as $comp) {
    $headers[] = $comp['name']; // Use component name exactly as header
}
fputcsv($output, $headers);

// Optional: Add a sample row (commented out or with a fake emp code so they understand)
$sample = ['EMP001', '30', '0'];
foreach ($components as $comp) {
    $sample[] = ''; // Leave blank to use employee's standard salary structure
}
fputcsv($output, $sample);

fclose($output);
exit;
