<?php
/**
 * Bulk Download Payslips (ZIP)
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/payroll_pdf_generator.php';

$batch_id = $_GET['batch_id'] ?? null;
if (!$batch_id) {
    die("Batch ID is required.");
}

// Check if ZipArchive exists
if (!class_exists('ZipArchive')) {
    die("Error: ZipArchive extension is not installed on this PHP server. Cannot generate bulk ZIP.");
}

// Fetch Batch
$stmt = $pdo->prepare("SELECT * FROM payroll_batches WHERE id = ?");
$stmt->execute([$batch_id]);
$batch = $stmt->fetch();

if (!$batch) {
    die("Batch not found.");
}

// Fetch all Payslips for this batch
$stmt_payslips = $pdo->prepare("SELECT id FROM payroll_payslips WHERE batch_id = ?");
$stmt_payslips->execute([$batch_id]);
$payslips = $stmt_payslips->fetchAll();

if (empty($payslips)) {
    die("No payslips found for this batch.");
}

// Create a temp directory
$tmp_dir = sys_get_temp_dir() . '/payslips_' . uniqid();
mkdir($tmp_dir);

$zip = new ZipArchive();
$zip_filename = sys_get_temp_dir() . '/Payslips_' . $batch['month'] . '_' . time() . '.zip';

if ($zip->open($zip_filename, ZipArchive::CREATE) !== TRUE) {
    die("Could not create ZIP file.");
}

$files_to_delete = [];

foreach ($payslips as $ps) {
    $filename = '';
    $dompdf = generate_payslip_pdf($pdo, $ps['id'], $filename);
    
    if ($dompdf && $filename) {
        $pdf_content = $dompdf->output();
        $file_path = $tmp_dir . '/' . $filename;
        file_put_contents($file_path, $pdf_content);
        $zip->addFile($file_path, $filename);
        $files_to_delete[] = $file_path;
    }
}

$zip->close();

// Stream to browser
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="Payslips_' . $batch['month'] . '.zip"');
header('Content-Length: ' . filesize($zip_filename));
readfile($zip_filename);

// Clean up
foreach ($files_to_delete as $file) {
    @unlink($file);
}
@rmdir($tmp_dir);
@unlink($zip_filename);

exit;
