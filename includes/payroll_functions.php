<?php
/**
 * Payroll Functions
 * Accredited Inspection Agency
 */

/**
 * Get all employees
 */
function get_payroll_employees($pdo, $status = null)
{
    $sql = "SELECT * FROM payroll_employees";
    $params = [];
    
    if ($status) {
        $sql .= " WHERE status = ?";
        $params[] = $status;
    }
    
    $sql .= " ORDER BY emp_code ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Get single employee by ID
 */
function get_payroll_employee($pdo, $id)
{
    $stmt = $pdo->prepare("SELECT * FROM payroll_employees WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Get all salary components
 */
function get_payroll_components($pdo)
{
    $stmt = $pdo->query("SELECT * FROM payroll_salary_components ORDER BY type DESC, id ASC");
    return $stmt->fetchAll();
}

/**
 * Get employee salary structure
 */
function get_employee_salary_structure($pdo, $employee_id)
{
    $stmt = $pdo->prepare("
        SELECT c.*, s.standard_amount 
        FROM payroll_salary_components c
        LEFT JOIN payroll_employee_salary_structure s ON c.id = s.component_id AND s.employee_id = ?
        ORDER BY c.type DESC, c.id ASC
    ");
    $stmt->execute([$employee_id]);
    return $stmt->fetchAll();
}

/**
 * Save employee and structure
 */
function save_payroll_employee($pdo, $data, $structure)
{
    $pdo->beginTransaction();
    try {
        if (empty($data['id'])) {
            $stmt = $pdo->prepare("
                INSERT INTO payroll_employees 
                (emp_code, name, designation, location, doj, bank_name, bank_account, pan_no, pf_no, uan_no, esic_no, email, status, standard_days, prev_month_lop, lop_reversal) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['emp_code'], $data['name'], $data['designation'], $data['location'], 
                $data['doj'] ?: null, $data['bank_name'], $data['bank_account'], $data['pan_no'], 
                $data['pf_no'], $data['uan_no'], $data['esic_no'], $data['email'], $data['status'],
                $data['standard_days'] ?? 0, $data['prev_month_lop'] ?? 0, $data['lop_reversal'] ?? 0
            ]);
            $employee_id = $pdo->lastInsertId();
        } else {
            $stmt = $pdo->prepare("
                UPDATE payroll_employees SET 
                emp_code = ?, name = ?, designation = ?, location = ?, doj = ?, bank_name = ?, 
                bank_account = ?, pan_no = ?, pf_no = ?, uan_no = ?, esic_no = ?, email = ?, status = ?,
                standard_days = ?, prev_month_lop = ?, lop_reversal = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $data['emp_code'], $data['name'], $data['designation'], $data['location'], 
                $data['doj'] ?: null, $data['bank_name'], $data['bank_account'], $data['pan_no'], 
                $data['pf_no'], $data['uan_no'], $data['esic_no'], $data['email'], $data['status'],
                $data['standard_days'] ?? 0, $data['prev_month_lop'] ?? 0, $data['lop_reversal'] ?? 0,
                $data['id']
            ]);
            $employee_id = $data['id'];
            
            // Delete old structure
            $stmt = $pdo->prepare("DELETE FROM payroll_employee_salary_structure WHERE employee_id = ?");
            $stmt->execute([$employee_id]);
        }

        // Insert new structure
        if (!empty($structure)) {
            $stmt = $pdo->prepare("
                INSERT INTO payroll_employee_salary_structure (employee_id, component_id, standard_amount) 
                VALUES (?, ?, ?)
            ");
            foreach ($structure as $comp_id => $amount) {
                // only save if amount is > 0 to save space, or save all? Save all provided.
                $stmt->execute([$employee_id, $comp_id, (float)$amount]);
            }
        }
        
        $pdo->commit();
        return $employee_id;
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}
