<?php
// require 'includes/db.php'; // REMOVED
require 'includes/SimpleXLSX.php';

$alias_map = [
    'house rent allowance' => 'HRA',
    'conveyance' => 'Conveyance Allowance',
    'basic salary' => 'Basic Salary',
    'provident fund' => 'PF Contribution',
    'esi' => 'ESIC Contribution'
];

$xlsx = Shuchkin\SimpleXLSX::parse('test_block.xlsx');
$rows = $xlsx->rows(0);

$output = [];
$current_emp = null;

foreach ($rows as $row) {
    if (isset($row[0]) && trim($row[0]) === 'Employee Code') {
        if ($current_emp !== null) {
            $output[] = $current_emp;
        }
        $current_emp = ['components' => []];
    }
    
    if ($current_emp !== null) {
        if (isset($row[3]) && trim($row[3]) !== '' && trim($row[3]) !== 'Components' && trim($row[3]) !== 'Employee Name' && trim($row[3]) !== 'Bank A/C No' && trim($row[3]) !== 'PAN' && trim($row[3]) !== 'PF UAN' && trim($row[3]) !== 'ESIC No' && trim($row[3]) !== 'Work Days' && trim($row[3]) !== 'LOP Days' && trim($row[3]) !== 'LOP Reversal Days' && trim($row[3]) !== 'Deductions') {
            $name = strtolower(trim(str_replace('_', '', $row[3])));
            $std_amount = trim($row[5] ?? '');
            if ($std_amount === '') {
                $std_amount = trim($row[4] ?? ''); // Fallback
            }
            if (is_numeric($std_amount)) {
                $clean_name = trim(str_replace('_', '', $row[3]));
                $lookup_name = strtolower(trim($clean_name));
                if (isset($alias_map[$lookup_name])) {
                    $c_exact = $alias_map[$lookup_name];
                } else {
                    $c_exact = $clean_name;
                }
                $current_emp['components'][$c_exact] = $std_amount;
            }
        }
    }
}
if ($current_emp !== null) {
    $output[] = $current_emp;
}
print_r($output);
