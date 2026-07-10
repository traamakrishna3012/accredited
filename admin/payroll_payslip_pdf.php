<?php
/**
 * Generate Single Payslip PDF
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/payroll_pdf_generator.php';

$payslip_id = $_GET['id'] ?? null;
if (!$payslip_id) {
    die("Payslip ID is required.");
}

$filename = '';
$dompdf = generate_payslip_pdf($pdo, $payslip_id, $filename);

if (!$dompdf) {
    die("Payslip not found.");
}

if (isset($_GET['action']) && $_GET['action'] == 'view') {
    $dompdf->stream($filename, ["Attachment" => false]);
} else {
    $dompdf->stream($filename, ["Attachment" => true]);
}
