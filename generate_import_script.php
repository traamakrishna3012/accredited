<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/SimpleXLSX.php';

// 1. Parse input Excel
$input_file = 'C:\Users\trama\Downloads\Salary_Slip_June_2026.xlsx';
$xlsx = Shuchkin\SimpleXLSX::parse($input_file);
if (!$xlsx) {
    die("Error parsing input: " . Shuchkin\SimpleXLSX::parseError());
}

$rows = $xlsx->rows(0);

$employees = [];
$current_emp = null;

$alias_map = [
    'house rent allowance' => 'HRA',
    'conveyance' => 'Conveyance Allowance',
    'basic salary' => 'Basic Salary',
    'provident fund' => 'PF Contribution'
];

// Get all DB components mapping to IDs
$stmt = $pdo->query("SELECT * FROM payroll_salary_components");
$db_components = $stmt->fetchAll(PDO::FETCH_ASSOC);
$comp_map = []; // name to id
foreach ($db_components as $comp) {
    $comp_map[strtolower(trim($comp['name']))] = $comp['id'];
}

foreach ($rows as $row) {
    if (isset($row[1]) && trim($row[1]) === 'Employee Code') {
        if ($current_emp !== null) {
            $employees[] = $current_emp;
        }
        $current_emp = [
            'emp_code' => trim($row[2] ?? ''),
            'name' => trim($row[5] ?? ''),
            'bank_name' => '',
            'bank_account' => '',
            'doj' => '',
            'pan_no' => '',
            'pf_no' => '',
            'uan_no' => '',
            'location' => '',
            'esic_no' => '',
            'designation' => '',
            'structure' => []
        ];
    }
    
    if ($current_emp !== null) {
        $c1 = trim($row[1] ?? '');
        $v1 = trim($row[2] ?? '');
        $c2 = trim($row[3] ?? '');
        $v2 = trim($row[5] ?? '');
        
        if ($c1 === 'Bank Name') $current_emp['bank_name'] = $v1;
        if ($c2 === 'Bank A/C No') $current_emp['bank_account'] = $v2;
        
        if ($c1 === 'DOJ') {
            // Format DOJ (could be YYYY-MM-DD HH:MM:SS)
            $doj = explode(' ', $v1)[0];
            $current_emp['doj'] = $doj;
        }
        if ($c2 === 'PAN') $current_emp['pan_no'] = $v2;
        
        if ($c1 === 'PF No.') $current_emp['pf_no'] = $v1;
        if ($c2 === 'PF UAN') $current_emp['uan_no'] = $v2;
        
        if ($c1 === 'Location') $current_emp['location'] = $v1;
        if ($c2 === 'ESIC No') $current_emp['esic_no'] = $v2;
        
        if ($c1 === 'Designation') $current_emp['designation'] = $v1;
        
        // Extract Earnings (Col 1 = Name, Col 2 = Standard Amount)
        if ($c1 !== '' && !in_array($c1, ['Employee Code', 'Bank Name', 'DOJ', 'PF No.', 'Location', 'Designation', 'Standard days', 'Previous Month LOP Days', 'Components', 'Total Earnings', 'Amount in words'])) {
            $clean_name = trim(str_replace('_', '', $c1));
            $lookup_name = strtolower(trim($clean_name));
            $std_amount = $v1;
            
            if (is_numeric($std_amount)) {
                $matched_id = null;
                
                if (isset($alias_map[$lookup_name])) {
                    $c_exact = strtolower(trim($alias_map[$lookup_name]));
                    $matched_id = $comp_map[$c_exact] ?? null;
                } else {
                    foreach ($comp_map as $c_lower => $cid) {
                        if ($lookup_name === $c_lower || strpos($lookup_name, $c_lower) !== false || levenshtein($lookup_name, $c_lower) < 3) {
                             $matched_id = $cid;
                             break;
                        }
                    }
                }
                
                if (!$matched_id) {
                    $stmt_ins = $pdo->prepare("INSERT INTO payroll_salary_components (name, type, is_prorated) VALUES (?, 'earning', 1)");
                    $stmt_ins->execute([$clean_name]);
                    $matched_id = $pdo->lastInsertId();
                    $comp_map[$lookup_name] = $matched_id;
                }
                
                if ($matched_id) {
                    $current_emp['structure'][$matched_id] = $std_amount;
                }
            }
        }
        
        // Extract Deductions (Col 4 = Name, Col 6 = Amount)
        $c_ded = trim($row[4] ?? '');
        $v_ded = trim($row[6] ?? '');
        if ($c_ded !== '' && $c_ded !== 'Components' && $c_ded !== 'Total Deductions') {
            $clean_name = trim($c_ded);
            $lookup_name = strtolower($clean_name);
            $amount = $v_ded;
            
            if (is_numeric($amount)) {
                $matched_id = null;
                
                if (isset($alias_map[$lookup_name])) {
                    $c_exact = strtolower(trim($alias_map[$lookup_name]));
                    $matched_id = $comp_map[$c_exact] ?? null;
                } else {
                    foreach ($comp_map as $c_lower => $cid) {
                        if ($lookup_name === $c_lower || strpos($lookup_name, $c_lower) !== false || levenshtein($lookup_name, $c_lower) < 3) {
                             $matched_id = $cid;
                             break;
                        }
                    }
                }
                
                if (!$matched_id) {
                    $stmt_ins = $pdo->prepare("INSERT INTO payroll_salary_components (name, type, is_prorated) VALUES (?, 'deduction', 0)");
                    $stmt_ins->execute([$clean_name]);
                    $matched_id = $pdo->lastInsertId();
                    $comp_map[$lookup_name] = $matched_id;
                }
                
                if ($matched_id) {
                    $current_emp['structure'][$matched_id] = $amount;
                }
            }
        }
    }
}

if ($current_emp !== null) {
    $employees[] = $current_emp;
}

// Generate the standalone PHP script
$php_code = "<?php\n";
$php_code .= "require_once __DIR__ . '/includes/db.php';\n\n";
$php_code .= "echo 'Starting employee import...<br>';\n\n";

$php_code .= "// 1. Sync Salary Components\n";
$stmt = $pdo->query("SELECT * FROM payroll_salary_components");
$all_comps = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($all_comps as $c) {
    $cid = $c['id'];
    $cname = addslashes($c['name']);
    $ctype = $c['type'];
    $cprorated = $c['is_prorated'];
    $php_code .= "\$pdo->prepare(\"INSERT IGNORE INTO payroll_salary_components (id, name, type, is_prorated) VALUES ($cid, '$cname', '$ctype', $cprorated)\")->execute();\n";
}
$php_code .= "\n";

foreach ($employees as $e) {
    $emp_code = addslashes($e['emp_code']);
    $name = addslashes($e['name']);
    $desig = addslashes($e['designation']);
    $loc = addslashes($e['location']);
    $doj = addslashes($e['doj']);
    $bank = addslashes($e['bank_name']);
    $acc = addslashes($e['bank_account']);
    $pan = addslashes($e['pan_no']);
    $pf = addslashes($e['pf_no']);
    $uan = addslashes($e['uan_no']);
    $esic = addslashes($e['esic_no']);
    
    $php_code .= "// Employee: $name\n";
    $php_code .= "\$stmt = \$pdo->prepare(\"INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\");\n";
    $php_code .= "\$stmt->execute(['$emp_code', '$name', '$desig', '$loc', '$doj', '$bank', '$acc', '$pan', '$pf', '$uan', '$esic']);\n";
    $php_code .= "\$emp_id = \$pdo->lastInsertId();\n";
    $php_code .= "if (!\$emp_id) {\n";
    $php_code .= "    \$stmt = \$pdo->prepare(\"SELECT id FROM payroll_employees WHERE emp_code = ?\");\n";
    $php_code .= "    \$stmt->execute(['$emp_code']);\n";
    $php_code .= "    \$emp_id = \$stmt->fetchColumn();\n";
    $php_code .= "}\n";
    
    // Check if new components exist in DB, if not insert them
    $php_code .= "// Sync structures\n";
    
    foreach ($e['structure'] as $cid => $amt) {
        $php_code .= "\$pdo->prepare(\"INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)\")->execute([\$emp_id, $cid, $amt]);\n";
    }
    $php_code .= "\n";
}

$php_code .= "echo 'Import completed successfully!';\n";
$php_code .= "?>\n";

file_put_contents(__DIR__ . '/live_import_employees.php', $php_code);
echo "Generated live_import_employees.php with " . count($employees) . " employees.\n";
