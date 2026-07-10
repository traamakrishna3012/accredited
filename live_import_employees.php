<?php
require_once __DIR__ . '/includes/db.php';

echo 'Starting employee import...<br>';

// 1. Sync Salary Components
$pdo->prepare("INSERT IGNORE INTO payroll_salary_components (id, name, type, is_prorated) VALUES (1, 'Basic Salary', 'earning', 1)")->execute();
$pdo->prepare("INSERT IGNORE INTO payroll_salary_components (id, name, type, is_prorated) VALUES (2, 'HRA', 'earning', 1)")->execute();
$pdo->prepare("INSERT IGNORE INTO payroll_salary_components (id, name, type, is_prorated) VALUES (3, 'Conveyance Allowance', 'earning', 1)")->execute();
$pdo->prepare("INSERT IGNORE INTO payroll_salary_components (id, name, type, is_prorated) VALUES (4, 'Medical Allowance', 'earning', 1)")->execute();
$pdo->prepare("INSERT IGNORE INTO payroll_salary_components (id, name, type, is_prorated) VALUES (5, 'Special Allowance', 'earning', 1)")->execute();
$pdo->prepare("INSERT IGNORE INTO payroll_salary_components (id, name, type, is_prorated) VALUES (6, 'PF Contribution', 'deduction', 1)")->execute();
$pdo->prepare("INSERT IGNORE INTO payroll_salary_components (id, name, type, is_prorated) VALUES (7, 'ESIC Contribution', 'deduction', 1)")->execute();
$pdo->prepare("INSERT IGNORE INTO payroll_salary_components (id, name, type, is_prorated) VALUES (8, 'Professional Tax', 'deduction', 0)")->execute();
$pdo->prepare("INSERT IGNORE INTO payroll_salary_components (id, name, type, is_prorated) VALUES (9, 'TDS', 'deduction', 0)")->execute();
$pdo->prepare("INSERT IGNORE INTO payroll_salary_components (id, name, type, is_prorated) VALUES (10, 'Provident Fund', 'deduction', 0)")->execute();
$pdo->prepare("INSERT IGNORE INTO payroll_salary_components (id, name, type, is_prorated) VALUES (11, 'Field/Laboratory Allowance', 'earning', 1)")->execute();
$pdo->prepare("INSERT IGNORE INTO payroll_salary_components (id, name, type, is_prorated) VALUES (12, 'Leave travel Allowance', 'earning', 1)")->execute();
$pdo->prepare("INSERT IGNORE INTO payroll_salary_components (id, name, type, is_prorated) VALUES (13, 'City Transfer Allowance', 'earning', 1)")->execute();
$pdo->prepare("INSERT IGNORE INTO payroll_salary_components (id, name, type, is_prorated) VALUES (14, 'ESI', 'deduction', 0)")->execute();

// Employee: ALKA JOSHI
$stmt = $pdo->prepare("INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['0001', 'ALKA JOSHI', 'DIRECTOR', 'RAIGARH', '2025-11-11', 'KOTAK', '7811474297', 'AUGPJ4887Q', 'CG/RAI/37194/28000', '102352231406', 'NA']);
$emp_id = $pdo->lastInsertId();
if (!$emp_id) {
    $stmt = $pdo->prepare("SELECT id FROM payroll_employees WHERE emp_code = ?");
    $stmt->execute(['0001']);
    $emp_id = $stmt->fetchColumn();
}
// Sync structures
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 1, 33000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 6, 4125]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 2, 6200]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 3, 6200]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 5, 5300]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 11, 1380]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 12, 1840]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 4, 1840]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 13, 3450]);

// Employee: AMIT  JOSHI
$stmt = $pdo->prepare("INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['0002', 'AMIT  JOSHI', 'DIRECTOR', 'RAIGARH', '2025-11-11', 'HDFC', '19871000002626', 'AOJPJ0865K', 'CG/RAI/37194/28000', '100098734166', 'NA']);
$emp_id = $pdo->lastInsertId();
if (!$emp_id) {
    $stmt = $pdo->prepare("SELECT id FROM payroll_employees WHERE emp_code = ?");
    $stmt->execute(['0002']);
    $emp_id = $stmt->fetchColumn();
}
// Sync structures
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 1, 33000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 6, 4125]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 2, 6200]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 3, 6200]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 5, 5300]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 11, 1380]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 12, 1840]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 4, 1840]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 13, 3450]);

// Employee: GOPAL KRISHNA
$stmt = $pdo->prepare("INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['0003', 'GOPAL KRISHNA', 'HR EXECUTIVE', 'RAIGARH', '2025-11-11', 'SBI', '11050762446', 'AIXPG3288K', 'CG/RAI/37194/28000', 'NA', 'NA']);
$emp_id = $pdo->lastInsertId();
if (!$emp_id) {
    $stmt = $pdo->prepare("SELECT id FROM payroll_employees WHERE emp_code = ?");
    $stmt->execute(['0003']);
    $emp_id = $stmt->fetchColumn();
}
// Sync structures
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 1, 12000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 5, 4200]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 11, 720]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 12, 960]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 4, 960]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 13, 1800]);

// Employee: BRIJESH SHARMA
$stmt = $pdo->prepare("INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['0004', 'BRIJESH SHARMA', 'QUALITY MANAGER', 'RAIGARH', '2025-11-11', 'HDFC', '50100058433231', 'EGPPS2803J', 'CG/RAI/37194/28000', '100497881266', 'NA']);
$emp_id = $pdo->lastInsertId();
if (!$emp_id) {
    $stmt = $pdo->prepare("SELECT id FROM payroll_employees WHERE emp_code = ?");
    $stmt->execute(['0004']);
    $emp_id = $stmt->fetchColumn();
}
// Sync structures
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 1, 16000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 6, 2000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 2, 6400]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 3, 6400]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 5, 5600]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 11, 960]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 12, 1280]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 4, 1280]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 13, 2400]);

// Employee: SHIVAM YADAV
$stmt = $pdo->prepare("INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['0005', 'SHIVAM YADAV', 'SUPERVISOR', 'RAIGARH', '2025-11-11', 'HDFC', '50100649432199', 'AKDPY5199F', 'CG/RAI/37194/28000', '101229065615', '']);
$emp_id = $pdo->lastInsertId();
if (!$emp_id) {
    $stmt = $pdo->prepare("SELECT id FROM payroll_employees WHERE emp_code = ?");
    $stmt->execute(['0005']);
    $emp_id = $stmt->fetchColumn();
}
// Sync structures
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 1, 10500]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 6, 0]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 3, 4200]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 14, 200]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 11, 630]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 12, 840]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 13, 1575]);

// Employee: SUBHANKAR PANDA
$stmt = $pdo->prepare("INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['0006', 'SUBHANKAR PANDA', 'Sr. Sampler', 'RAIGARH', '2025-11-11', 'HDFC', '07631050008803', 'DCAPP1272A', 'CG/RAI/37194/28000', '100384703115', 'NA']);
$emp_id = $pdo->lastInsertId();
if (!$emp_id) {
    $stmt = $pdo->prepare("SELECT id FROM payroll_employees WHERE emp_code = ?");
    $stmt->execute(['0006']);
    $emp_id = $stmt->fetchColumn();
}
// Sync structures
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 1, 12000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 6, 1500]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 5, 4200]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 11, 720]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 12, 960]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 4, 960]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 13, 1800]);

// Employee: RAHUL MISHRA
$stmt = $pdo->prepare("INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['0007', 'RAHUL MISHRA', 'PA', 'RAIGARH', '2025-11-11', 'KOTAk', '2445491085', 'GZSPM3260D', 'CG/RAI/37194/28000', '101866820157', 'NA']);
$emp_id = $pdo->lastInsertId();
if (!$emp_id) {
    $stmt = $pdo->prepare("SELECT id FROM payroll_employees WHERE emp_code = ?");
    $stmt->execute(['0007']);
    $emp_id = $stmt->fetchColumn();
}
// Sync structures
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 1, 12000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 6, 1500]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 5, 4200]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 11, 720]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 12, 960]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 4, 960]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 13, 1800]);

// Employee: SUDIPTA DAS GUPTA
$stmt = $pdo->prepare("INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['0008', 'SUDIPTA DAS GUPTA', 'BRANCH MANAGER', 'RAIGARH', '2025-11-11', 'HDFC', '09181050000922', 'AKPPD9417K', 'CG/RAI/37194/28000', '100385096190', 'NA']);
$emp_id = $pdo->lastInsertId();
if (!$emp_id) {
    $stmt = $pdo->prepare("SELECT id FROM payroll_employees WHERE emp_code = ?");
    $stmt->execute(['0008']);
    $emp_id = $stmt->fetchColumn();
}
// Sync structures
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 1, 14000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 6, 1750]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 2, 4900]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 3, 4900]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 5, 4200]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 11, 840]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 12, 980]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 4, 980]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 13, 2100]);

// Employee: DEEPAK SINHA
$stmt = $pdo->prepare("INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['0010', 'DEEPAK SINHA', 'AGM (Marketing)', 'RAIGARH', '2026-02-09', 'Bandhan Bank', '50190035634058', 'BSLPS9412E', 'CG/RAI/37194/28000', '100550144035', 'NA']);
$emp_id = $pdo->lastInsertId();
if (!$emp_id) {
    $stmt = $pdo->prepare("SELECT id FROM payroll_employees WHERE emp_code = ?");
    $stmt->execute(['0010']);
    $emp_id = $stmt->fetchColumn();
}
// Sync structures
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 1, 17000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 6, 2125]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 2, 6800]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 3, 6800]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 5, 5950]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 11, 1020]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 12, 1360]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 4, 1360]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 13, 2550]);

// Employee: SUBHANKAR BERA
$stmt = $pdo->prepare("INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['0014', 'SUBHANKAR BERA', 'Sr. Sampler', 'RAIGARH', '2026-01-05', 'PNB', '2521000103244493', 'ECIPB4685Q', 'CG/RAI/37194/28000', '101464214043', 'NA']);
$emp_id = $pdo->lastInsertId();
if (!$emp_id) {
    $stmt = $pdo->prepare("SELECT id FROM payroll_employees WHERE emp_code = ?");
    $stmt->execute(['0014']);
    $emp_id = $stmt->fetchColumn();
}
// Sync structures
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 1, 10500]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 6, 1312.5]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 3, 2000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 14, 200]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 11, 630]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 12, 840]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 13, 1575]);

// Employee: BISWAJIT TRIPATHY
$stmt = $pdo->prepare("INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['0015', 'BISWAJIT TRIPATHY', 'Executive', 'ROULKELA', '2026-02-04', 'BOI', '520610110006852', 'ADHPT6549H', 'CG/RAI/37194/28000', '101262387660', 'NA']);
$emp_id = $pdo->lastInsertId();
if (!$emp_id) {
    $stmt = $pdo->prepare("SELECT id FROM payroll_employees WHERE emp_code = ?");
    $stmt->execute(['0015']);
    $emp_id = $stmt->fetchColumn();
}
// Sync structures
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 1, 13000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 6, 1625]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 2, 3320]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 3, 3320]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 5, 2400]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 11, 780]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 12, 865]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 4, 865]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 13, 1950]);

// Employee: DEEPAK YADAV
$stmt = $pdo->prepare("INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['0016', 'DEEPAK YADAV', 'Sampler', 'ROULKELA', '2026-02-04', 'BOI', '557010510001319', 'BFUPY3072E', 'CG/RAI/37194/28000', '101461357303', 'NA']);
$emp_id = $pdo->lastInsertId();
if (!$emp_id) {
    $stmt = $pdo->prepare("SELECT id FROM payroll_employees WHERE emp_code = ?");
    $stmt->execute(['0016']);
    $emp_id = $stmt->fetchColumn();
}
// Sync structures
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 1, 10500]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 6, 1312.5]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 3, 2000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 14, 200]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 11, 630]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 5, 2000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 13, 1575]);

// Employee: SUCHIT JOJO
$stmt = $pdo->prepare("INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['0017', 'SUCHIT JOJO', 'Sampler', 'ROULKELA', '2026-02-04', 'SBI', '39996052004', 'CNZPJ1932N', 'CG/RAI/37194/28000', '101810878706', 'NA']);
$emp_id = $pdo->lastInsertId();
if (!$emp_id) {
    $stmt = $pdo->prepare("SELECT id FROM payroll_employees WHERE emp_code = ?");
    $stmt->execute(['0017']);
    $emp_id = $stmt->fetchColumn();
}
// Sync structures
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 1, 10500]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 6, 1312.5]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 3, 2000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 14, 200]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 11, 630]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 5, 2000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 13, 1575]);

// Employee: BINOD KHADIYA
$stmt = $pdo->prepare("INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['0018', 'BINOD KHADIYA', 'Sampler', 'ROULKELA', '2026-02-06', 'INDIAN BANK', '50370048039', 'JAFPK7970H', 'CG/RAI/37194/28000', 'NA', 'NA']);
$emp_id = $pdo->lastInsertId();
if (!$emp_id) {
    $stmt = $pdo->prepare("SELECT id FROM payroll_employees WHERE emp_code = ?");
    $stmt->execute(['0018']);
    $emp_id = $stmt->fetchColumn();
}
// Sync structures
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 1, 10000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 6, 483.87096774194]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 3, 1500]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 14, 200]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 11, 630]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 13, 1575]);

// Employee: ANJOR TOPNO
$stmt = $pdo->prepare("INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['0019', 'ANJOR TOPNO', 'Sampler', 'ROULKELA', '2026-02-11', 'UCO Bank', '07923211013891', 'AQEPT8952A', 'CG/RAI/37194/28000', '100519254221', 'NA']);
$emp_id = $pdo->lastInsertId();
if (!$emp_id) {
    $stmt = $pdo->prepare("SELECT id FROM payroll_employees WHERE emp_code = ?");
    $stmt->execute(['0019']);
    $emp_id = $stmt->fetchColumn();
}
// Sync structures
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 1, 10500]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 6, 1312.5]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 3, 2000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 14, 200]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 11, 630]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 5, 2000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 13, 1575]);

// Employee: DIWAKAR KUMAR PASWAN
$stmt = $pdo->prepare("INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['0020', 'DIWAKAR KUMAR PASWAN', 'Sampler', 'ROULKELA', '2026-02-12', 'SBI', '40401409260', 'EXUPP4057D', 'CG/RAI/37194/28000', '101929573259', 'NA']);
$emp_id = $pdo->lastInsertId();
if (!$emp_id) {
    $stmt = $pdo->prepare("SELECT id FROM payroll_employees WHERE emp_code = ?");
    $stmt->execute(['0020']);
    $emp_id = $stmt->fetchColumn();
}
// Sync structures
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 1, 10500]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 6, 508.06451612903]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 3, 2300]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 14, 200]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 11, 630]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 5, 2000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 13, 1575]);

// Employee: ANAND KUMAR SAMASI
$stmt = $pdo->prepare("INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['0021', 'ANAND KUMAR SAMASI', 'Sampler', 'ROULKELA', '2026-02-21', 'SBI', '33223786461', 'FADPS2255F', 'CG/RAI/37194/28000', '101194966904', 'NA']);
$emp_id = $pdo->lastInsertId();
if (!$emp_id) {
    $stmt = $pdo->prepare("SELECT id FROM payroll_employees WHERE emp_code = ?");
    $stmt->execute(['0021']);
    $emp_id = $stmt->fetchColumn();
}
// Sync structures
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 1, 10000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 6, 1250]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 3, 1500]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 14, 200]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 11, 630]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 13, 1570]);

// Employee: SURENDRA PASWAN
$stmt = $pdo->prepare("INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['0022', 'SURENDRA PASWAN', 'Sampler', 'ROULKELA', '2026-03-14', 'SBI', '38160459061', 'FQKPP9382M', 'CG/RAI/37194/28000', '', 'NA']);
$emp_id = $pdo->lastInsertId();
if (!$emp_id) {
    $stmt = $pdo->prepare("SELECT id FROM payroll_employees WHERE emp_code = ?");
    $stmt->execute(['0022']);
    $emp_id = $stmt->fetchColumn();
}
// Sync structures
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 1, 10500]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 6, 508.06451612903]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 3, 2300]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 14, 200]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 11, 630]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 5, 2000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 13, 1575]);

// Employee: SAGAR KUMAR PASWAN
$stmt = $pdo->prepare("INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['0023', 'SAGAR KUMAR PASWAN', 'Sampler', 'ROULKELA', '2026-03-15', 'SBI', '587302010006560', 'FWIPP6100D', 'CG/RAI/37194/28000', '', 'NA']);
$emp_id = $pdo->lastInsertId();
if (!$emp_id) {
    $stmt = $pdo->prepare("SELECT id FROM payroll_employees WHERE emp_code = ?");
    $stmt->execute(['0023']);
    $emp_id = $stmt->fetchColumn();
}
// Sync structures
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 1, 10500]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 6, 508.06451612903]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 3, 2300]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 14, 200]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 11, 630]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 5, 2000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 13, 1575]);

// Employee: RAHUL KUMAR PATEL
$stmt = $pdo->prepare("INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['0024', 'RAHUL KUMAR PATEL', 'SUPERVISOR', 'SINGARULI', '2026-05-15', 'Union Bank of India', '91580212000036', 'IDNPP1464F', 'CG/RAI/37194/28000', '', 'NA']);
$emp_id = $pdo->lastInsertId();
if (!$emp_id) {
    $stmt = $pdo->prepare("SELECT id FROM payroll_employees WHERE emp_code = ?");
    $stmt->execute(['0024']);
    $emp_id = $stmt->fetchColumn();
}
// Sync structures
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 1, 10500]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 6, 254.03225806452]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 3, 2300]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 14, 200]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 11, 630]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 5, 2000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 13, 1575]);

// Employee: SATISH KUMAR PATEL
$stmt = $pdo->prepare("INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['0025', 'SATISH KUMAR PATEL', 'Sampler', 'SINGARULI', '2026-05-15', 'Indian Bank', '8229856248', 'EZZPP8320Q', 'CG/RAI/37194/28000', '101215451808', 'NA']);
$emp_id = $pdo->lastInsertId();
if (!$emp_id) {
    $stmt = $pdo->prepare("SELECT id FROM payroll_employees WHERE emp_code = ?");
    $stmt->execute(['0025']);
    $emp_id = $stmt->fetchColumn();
}
// Sync structures
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 1, 10000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 6, 241.93548387097]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 3, 1500]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 14, 200]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 11, 630]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 5, 1000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 13, 1570]);

// Employee: AVIJIT MAJI
$stmt = $pdo->prepare("INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['0026', 'AVIJIT MAJI', 'SUPERVISOR', 'RAIGARH', '2026-05-18', 'STATE BANK OF INDIA', '20376787895', 'CXUPM2834B', 'CG/RAI/37194/28000', '101150340907', 'NA']);
$emp_id = $pdo->lastInsertId();
if (!$emp_id) {
    $stmt = $pdo->prepare("SELECT id FROM payroll_employees WHERE emp_code = ?");
    $stmt->execute(['0026']);
    $emp_id = $stmt->fetchColumn();
}
// Sync structures
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 1, 12000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 6, 1500]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 5, 4200]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 14, 200]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 11, 720]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 3, 2000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 13, 1570]);

// Employee: UPDESH YADAV
$stmt = $pdo->prepare("INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['0027', 'UPDESH YADAV', 'Sr. Chemist', 'Sambalpur', '2026-05-25', 'United Bank', '50100389660212', 'AHHPY9511E', 'CG/RAI/37194/28000', '100550590277', 'NA']);
$emp_id = $pdo->lastInsertId();
if (!$emp_id) {
    $stmt = $pdo->prepare("SELECT id FROM payroll_employees WHERE emp_code = ?");
    $stmt->execute(['0027']);
    $emp_id = $stmt->fetchColumn();
}
// Sync structures
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 1, 20000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 6, 2177.4193548387]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 2, 3800]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 3, 3800]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 5, 2950]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 11, 1020]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 12, 1360]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 4, 1360]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 13, 2550]);

// Employee: RAM PRASAD SAHU
$stmt = $pdo->prepare("INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['0029', 'RAM PRASAD SAHU', 'Sampler', 'KATNI', '2026-06-01', 'Union Bank of India', '465102010968408', 'FWDPS5133L', 'CG/RAI/37194/28000', '100913735610', 'NA']);
$emp_id = $pdo->lastInsertId();
if (!$emp_id) {
    $stmt = $pdo->prepare("SELECT id FROM payroll_employees WHERE emp_code = ?");
    $stmt->execute(['0029']);
    $emp_id = $stmt->fetchColumn();
}
// Sync structures
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 1, 10000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 6, 806.45161290323]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 3, 1500]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 14, 200]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 11, 630]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 13, 1575]);

// Employee: RAKESH KUMAR
$stmt = $pdo->prepare("INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['0030', 'RAKESH KUMAR', 'Sr. Supervisor', 'JHARSUGUDA', '2025-12-25', 'HDFC', '50100028684610', 'BHEPK0718D', 'CG/RAI/37194/28000', '100318656341', 'NA']);
$emp_id = $pdo->lastInsertId();
if (!$emp_id) {
    $stmt = $pdo->prepare("SELECT id FROM payroll_employees WHERE emp_code = ?");
    $stmt->execute(['0030']);
    $emp_id = $stmt->fetchColumn();
}
// Sync structures
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 1, 18000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 6, 1379.0322580645]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 2, 3320]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 3, 3320]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 5, 2000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 11, 780]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 12, 865]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 4, 865]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 13, 1350]);

// Employee: RITESH YADAV
$stmt = $pdo->prepare("INSERT IGNORE INTO payroll_employees (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['0031', 'RITESH YADAV', 'Chemist', 'JHARSUGUDA', '2026-02-04', 'Union Bank of India', '695102010010493', 'BDYPY4312D', 'CG/RAI/37194/28000', '102281877946', 'NA']);
$emp_id = $pdo->lastInsertId();
if (!$emp_id) {
    $stmt = $pdo->prepare("SELECT id FROM payroll_employees WHERE emp_code = ?");
    $stmt->execute(['0031']);
    $emp_id = $stmt->fetchColumn();
}
// Sync structures
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 1, 12500]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 6, 907.25806451613]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 2, 3020]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 3, 3020]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 5, 2000]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 11, 780]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 12, 865]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 4, 865]);
$pdo->prepare("INSERT IGNORE INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE standard_amount = VALUES(standard_amount)")->execute([$emp_id, 13, 1550]);

echo 'Import completed successfully!';
?>
