<?php
require 'includes/SimpleXLSX.php';

$xlsx = Shuchkin\SimpleXLSX::parse('test_block.xlsx');
$rows = $xlsx->rows(0);

$current_emp = null;
foreach ($rows as $row) {
    if (isset($row[0]) && trim($row[0]) === 'Employee Code') {
        if ($current_emp !== null) {
            print_r($current_emp);
        }
        $current_emp = ['Work_Days' => 0, 'LOP_Days' => 0];
    }
    
    if ($current_emp !== null) {
        if (isset($row[3]) && trim($row[3]) === 'Work Days') {
            $current_emp['Work_Days'] = trim($row[5] ?? '');
        }
        if (isset($row[3]) && trim($row[3]) === 'LOP Days') {
            $current_emp['LOP_Days'] = trim($row[5] ?? '');
        }
        
        // Wait, what if Work Days is in row[2]? Let's check all columns to see where "Work Days" is
        foreach ($row as $idx => $val) {
            if (trim($val) === 'Work Days') {
                $current_emp['Work_Days_Found_At'] = $idx;
                $current_emp['Work_Days_Value_Next'] = $row[$idx+1] ?? '';
            }
        }
    }
}
if ($current_emp !== null) print_r($current_emp);
