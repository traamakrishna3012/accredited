<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/SimpleXLSX.php';
require_once __DIR__ . '/includes/SimpleXLSXGen.php';

// 1. Get database components to know the headers
$stmt = $pdo->query("SELECT * FROM payroll_salary_components ORDER BY type DESC, id ASC");
$db_components = $stmt->fetchAll(PDO::FETCH_ASSOC);

$headers = ['Emp_Code', 'Work_Days', 'LOP_Days'];
$comp_map = []; // lowercase name to exact name
foreach ($db_components as $comp) {
    $headers[] = $comp['name'];
    $comp_map[strtolower(trim($comp['name']))] = $comp['name'];
}

// 2. Parse input Excel
$input_file = 'C:\Users\trama\Downloads\Salary_Slip_June_2026.xlsx';
$xlsx = Shuchkin\SimpleXLSX::parse($input_file);
if (!$xlsx) {
    die("Error parsing input: " . Shuchkin\SimpleXLSX::parseError());
}

$rows = $xlsx->rows(0);

$output_data = [];
// We will assign $output_data[0] = $headers at the end!

$current_emp = null;

// Hardcoded alias map for common mismatches
$alias_map = [
    'house rent allowance' => 'HRA',
    'conveyance' => 'Conveyance Allowance',
    'basic salary' => 'Basic Salary',
    'provident fund' => 'PF Contribution'
];

foreach ($rows as $row) {
    // Check if new employee starts
    if (isset($row[1]) && trim($row[1]) === 'Employee Code') {
        // Save previous employee
        if ($current_emp !== null) {
            $out_row = [
                $current_emp['Emp_Code'],
                $current_emp['Work_Days'],
                $current_emp['LOP_Days']
            ];
            foreach ($db_components as $comp) {
                $out_row[] = $current_emp['components'][$comp['name']] ?? '';
            }
            $output_data[] = $out_row;
        }

        // Start new employee
        $current_emp = [
            'Emp_Code' => trim($row[2] ?? ''),
            'Work_Days' => 0,
            'LOP_Days' => 0,
            'components' => []
        ];
    }
    
    if ($current_emp !== null) {
        // Extract Work Days
        if (isset($row[3]) && trim($row[3]) === 'Work Days') {
            $current_emp['Work_Days'] = trim($row[5] ?? '');
        }
        // Extract LOP Days
        if (isset($row[3]) && trim($row[3]) === 'LOP Days') {
            $current_emp['LOP_Days'] = trim($row[5] ?? '');
        }

        // Extract Earnings (Col 1 = Name, Col 2 = Standard Amount)
        if (isset($row[1]) && trim($row[1]) !== '' && trim($row[1]) !== 'Employee Code' && trim($row[1]) !== 'Bank Name' && trim($row[1]) !== 'DOJ' && trim($row[1]) !== 'PF No.' && trim($row[1]) !== 'Location' && trim($row[1]) !== 'Designation' && trim($row[1]) !== 'Standard days' && trim($row[1]) !== 'Previous Month LOP Days' && trim($row[1]) !== 'Components' && trim($row[1]) !== 'Total Earnings' && trim($row[1]) !== 'Amount in words') {
            $name = strtolower(trim(str_replace('_', '', $row[1]))); // Basic_ Salary -> basic salary
            $std_amount = trim($row[2] ?? '');
            if (is_numeric($std_amount)) {
                $found = false;
                $clean_name = trim(str_replace('_', '', $row[1]));
                $lookup_name = strtolower(trim($clean_name));
                
                // Check aliases first
                if (isset($alias_map[$lookup_name])) {
                    $c_exact = $alias_map[$lookup_name];
                    $current_emp['components'][$c_exact] = $std_amount;
                    $found = true;
                } else {
                    // Find matching DB component
                    foreach ($comp_map as $c_lower => $c_exact) {
                        if ($lookup_name === $c_lower || strpos($lookup_name, $c_lower) !== false || levenshtein($lookup_name, $c_lower) < 3) {
                             $current_emp['components'][$c_exact] = $std_amount;
                             $found = true;
                             break;
                        }
                    }
                }
                
                if (!isset($found) || !$found) {
                    // Insert missing earning component
                    $clean_name = trim(str_replace('_', '', $row[1]));
                    if ($clean_name === 'Conveyance') $clean_name = 'Conveyance Allowance';
                    $stmt_ins = $pdo->prepare("INSERT INTO payroll_salary_components (name, type, is_prorated) VALUES (?, 'earning', 1)");
                    $stmt_ins->execute([$clean_name]);
                    
                    // Refresh comp_map
                    $comp_map[strtolower(trim($clean_name))] = $clean_name;
                    $db_components[] = ['name' => $clean_name];
                    if (!in_array($clean_name, $headers)) {
                        $headers[] = $clean_name;
                    }
                    $current_emp['components'][$clean_name] = $std_amount;
                }
            }
        }
        
        // Extract Deductions (Col 4 = Name, Col 6 = Amount)
        if (isset($row[4]) && trim($row[4]) !== '' && trim($row[4]) !== 'Components' && trim($row[4]) !== 'Total Deductions') {
            $name = strtolower(trim($row[4]));
            $amount = trim($row[6] ?? '');
            if (is_numeric($amount)) {
                $found_ded = false;
                $clean_name = trim($row[4]);
                $lookup_name = strtolower($clean_name);
                
                // Check aliases first
                if (isset($alias_map[$lookup_name])) {
                    $c_exact = $alias_map[$lookup_name];
                    $current_emp['components'][$c_exact] = $amount;
                    $found_ded = true;
                } else {
                    // Find matching DB component
                    foreach ($comp_map as $c_lower => $c_exact) {
                        if ($lookup_name === $c_lower || strpos($lookup_name, $c_lower) !== false || levenshtein($lookup_name, $c_lower) < 3) {
                             $current_emp['components'][$c_exact] = $amount;
                             $found_ded = true;
                             break;
                        }
                    }
                }
                
                if (!isset($found_ded) || !$found_ded) {
                    // Insert missing deduction component
                    $clean_name = trim($row[4]);
                    $stmt_ins = $pdo->prepare("INSERT INTO payroll_salary_components (name, type, is_prorated) VALUES (?, 'deduction', 0)");
                    $stmt_ins->execute([$clean_name]);
                    
                    // Refresh comp_map
                    $comp_map[strtolower(trim($clean_name))] = $clean_name;
                    $db_components[] = ['name' => $clean_name];
                    if (!in_array($clean_name, $headers)) {
                        $headers[] = $clean_name;
                    }
                    $current_emp['components'][$clean_name] = $amount;
                }
            }
        }
    }
}

// Push last employee
if ($current_emp !== null) {
    $out_row = [
        $current_emp['Emp_Code'],
        $current_emp['Work_Days'],
        $current_emp['LOP_Days']
    ];
    foreach ($db_components as $comp) {
        $out_row[] = $current_emp['components'][$comp['name']] ?? '';
    }
    $output_data[] = $out_row;
}

// 3. Generate output Excel
// We must prepend the headers to the output_data array so that any new dynamically added components are included!
array_unshift($output_data, $headers);
$out_xlsx = Shuchkin\SimpleXLSXGen::fromArray($output_data);
$output_file = 'C:\Users\trama\Downloads\bulk_upload_formatted.xlsx';
$out_xlsx->saveAs($output_file);

echo "Successfully converted to: " . $output_file . "\n";
print_r($output_data);

