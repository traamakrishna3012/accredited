<?php
require 'includes/db.php';

try {
    $pdo->beginTransaction();

    // 1. Delete ESI (id=14) and Provident Fund (id=10) from payslip_items
    $pdo->exec("DELETE FROM payroll_payslip_items WHERE component_id IN (SELECT id FROM payroll_salary_components WHERE name IN ('ESI', 'Provident Fund'))");

    // 2. Delete ESI and Provident Fund from standard structure
    $pdo->exec("DELETE FROM payroll_employee_salary_structure WHERE component_id IN (SELECT id FROM payroll_salary_components WHERE name IN ('ESI', 'Provident Fund'))");

    // 3. Delete ESI and Provident Fund from components
    $pdo->exec("DELETE FROM payroll_salary_components WHERE name IN ('ESI', 'Provident Fund')");

    // 4. Recalculate totals for all payslips!
    $stmt = $pdo->query("SELECT id FROM payroll_payslips");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $payslip_id = $row['id'];
        
        $c_stmt = $pdo->prepare("
            SELECT pc.actual_amount, sc.type 
            FROM payroll_payslip_items pc
            JOIN payroll_salary_components sc ON pc.component_id = sc.id
            WHERE pc.payslip_id = ?
        ");
        $c_stmt->execute([$payslip_id]);
        $earnings = 0;
        $deductions = 0;
        while ($c = $c_stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($c['type'] === 'earning') {
                $earnings += $c['actual_amount'];
            } else {
                $deductions += $c['actual_amount'];
            }
        }
        $net_pay = $earnings - $deductions;
        
        $u_stmt = $pdo->prepare("UPDATE payroll_payslips SET total_earnings = ?, total_deductions = ?, net_pay = ? WHERE id = ?");
        $u_stmt->execute([$earnings, $deductions, $net_pay, $payslip_id]);
    }
    
    $pdo->commit();
    echo "Fixed DB successfully.\n";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
