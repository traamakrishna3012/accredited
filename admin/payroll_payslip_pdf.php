<?php
/**
 * Generate Payslip PDF
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/payroll_functions.php';
require_once __DIR__ . '/../includes/payroll_calculator.php';

// Include Dompdf directly (assuming manual install at includes/dompdf)
require_once __DIR__ . '/../includes/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

$payslip_id = $_GET['id'] ?? null;
if (!$payslip_id) {
    die("Payslip ID is required.");
}

// Fetch Payslip Data
$stmt = $pdo->prepare("
    SELECT p.*, b.month, b.standard_working_days 
    FROM payroll_payslips p
    JOIN payroll_batches b ON p.batch_id = b.id
    WHERE p.id = ?
");
$stmt->execute([$payslip_id]);
$payslip = $stmt->fetch();

if (!$payslip) {
    die("Payslip not found.");
}

// Fetch Employee Data
$stmt_emp = $pdo->prepare("SELECT * FROM payroll_employees WHERE id = ?");
$stmt_emp->execute([$payslip['employee_id']]);
$emp = $stmt_emp->fetch();

// Fetch Items
$stmt_items = $pdo->prepare("
    SELECT i.*, c.name, c.type 
    FROM payroll_payslip_items i
    JOIN payroll_salary_components c ON i.component_id = c.id
    WHERE i.payslip_id = ?
    ORDER BY c.type ASC, c.name ASC
");
$stmt_items->execute([$payslip_id]);
$items = $stmt_items->fetchAll();

$earnings = array_filter($items, fn($i) => $i['type'] === 'earning');
$deductions = array_filter($items, fn($i) => $i['type'] === 'deduction');

$month_formatted = date('F Y', strtotime($payslip['month'] . '-01'));
$site_name = defined('SITE_NAME') ? SITE_NAME : 'Accredited Inspection Agency';

// Build HTML Template
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: "Helvetica", "Arial", sans-serif; font-size: 11px; color: #333; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .mb-4 { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { padding: 6px; border: 1px solid #ccc; }
        .no-border th, .no-border td { border: none; padding: 4px; }
        .bg-light { background-color: #f8f9fa; }
        .company-header { border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px; }
        .company-title { font-size: 18px; font-weight: bold; text-transform: uppercase; }
        
        .grid-table { width: 100%; border: 1px solid #000; margin-bottom: 15px; }
        .grid-table th, .grid-table td { border: 1px solid #000; padding: 5px; }
        .grid-table th { background-color: #f0f0f0; text-align: left; width: 25%; }
        .grid-table td { width: 25%; }

        .salary-table { width: 100%; border: 1px solid #000; }
        .salary-table th, .salary-table td { border: 1px solid #000; padding: 5px; }
        .salary-table th { background-color: #f0f0f0; text-align: center; }
        
        .col-half { width: 50%; vertical-align: top; }
        
        .inner-table { width: 100%; border-collapse: collapse; }
        .inner-table td { border: none; border-bottom: 1px solid #eee; }
        .inner-table td:last-child { border-bottom: none; text-align: right; }
        
        .signature-box { margin-top: 50px; text-align: right; padding-right: 20px; }
    </style>
</head>
<body>
    <div class="company-header text-center">
        <div class="company-title">' . htmlspecialchars($site_name) . '</div>
        <div>Payslip for the month of ' . $month_formatted . '</div>
    </div>

    <table class="grid-table">
        <tr>
            <th>Employee Name</th>
            <td>' . htmlspecialchars($emp['name']) . '</td>
            <th>Employee Code</th>
            <td>' . htmlspecialchars($emp['emp_code']) . '</td>
        </tr>
        <tr>
            <th>Designation</th>
            <td>' . htmlspecialchars($emp['designation']) . '</td>
            <th>Location</th>
            <td>' . htmlspecialchars($emp['location']) . '</td>
        </tr>
        <tr>
            <th>Date of Joining</th>
            <td>' . ($emp['doj'] ? date('d-M-Y', strtotime($emp['doj'])) : '') . '</td>
            <th>UAN / PF No</th>
            <td>' . htmlspecialchars($emp['uan']) . ' / ' . htmlspecialchars($emp['pf_no']) . '</td>
        </tr>
        <tr>
            <th>PAN</th>
            <td>' . htmlspecialchars($emp['pan']) . '</td>
            <th>ESIC No</th>
            <td>' . htmlspecialchars($emp['esic_no']) . '</td>
        </tr>
        <tr>
            <th>Bank Name</th>
            <td>' . htmlspecialchars($emp['bank_name']) . '</td>
            <th>Bank Account No</th>
            <td>' . htmlspecialchars($emp['bank_account']) . '</td>
        </tr>
        <tr>
            <th>Total Working Days</th>
            <td>' . $payslip['standard_working_days'] . '</td>
            <th>Paid Days / LOP</th>
            <td>' . $payslip['work_days'] . ' / ' . $payslip['lop_days'] . '</td>
        </tr>
    </table>

    <table class="salary-table">
        <tr>
            <th colspan="3">Earnings</th>
            <th colspan="3">Deductions</th>
        </tr>
        <tr>
            <th style="width: 25%">Component</th>
            <th style="width: 12.5%">Standard</th>
            <th style="width: 12.5%">Actual</th>
            <th style="width: 25%">Component</th>
            <th style="width: 12.5%">Standard</th>
            <th style="width: 12.5%">Actual</th>
        </tr>
        <tr>
            <td colspan="3" class="col-half" style="padding:0;">
                <table class="inner-table">';
                
foreach ($earnings as $e) {
    $html .= '<tr>
                <td style="width:50%">' . htmlspecialchars($e['name']) . '</td>
                <td style="width:25%; text-align:right;">' . number_format($e['standard_amount'], 2) . '</td>
                <td style="width:25%; text-align:right;">' . number_format($e['actual_amount'], 2) . '</td>
              </tr>';
}

$html .= '      </table>
            </td>
            <td colspan="3" class="col-half" style="padding:0;">
                <table class="inner-table">';

foreach ($deductions as $d) {
    $html .= '<tr>
                <td style="width:50%">' . htmlspecialchars($d['name']) . '</td>
                <td style="width:25%; text-align:right;">' . number_format($d['standard_amount'], 2) . '</td>
                <td style="width:25%; text-align:right;">' . number_format($d['actual_amount'], 2) . '</td>
              </tr>';
}

$html .= '      </table>
            </td>
        </tr>
        <tr class="bg-light fw-bold">
            <td>Total Earnings</td>
            <td></td>
            <td class="text-right">' . number_format($payslip['total_earnings'], 2) . '</td>
            <td>Total Deductions</td>
            <td></td>
            <td class="text-right">' . number_format($payslip['total_deductions'], 2) . '</td>
        </tr>
        <tr class="fw-bold">
            <td colspan="4" class="text-right">NET PAY</td>
            <td colspan="2" class="text-right" style="font-size: 14px;">Rs. ' . number_format($payslip['net_pay'], 2) . '</td>
        </tr>
    </table>

    <div class="mb-4">
        <strong>Amount in words:</strong><br>
        ' . number_to_indian_words($payslip['net_pay']) . '
    </div>

    <div class="mb-4" style="font-size: 10px; color: #555;">
        <em>Note: This is a computer generated payslip and does not require a physical signature.</em>
    </div>
    
    <div class="signature-box">
        <div>_______________________</div>
        <div style="margin-top: 5px;">Authorized Signatory</div>
    </div>
</body>
</html>';

// Initialize Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Helvetica');
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$filename = 'Payslip_' . str_replace(' ', '_', $emp['name']) . '_' . $month_formatted . '.pdf';

if (isset($_GET['action']) && $_GET['action'] == 'view') {
    $dompdf->stream($filename, ["Attachment" => false]);
} else {
    $dompdf->stream($filename, ["Attachment" => true]);
}
