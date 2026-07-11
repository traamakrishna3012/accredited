<?php
require 'includes/db.php';
$stmt = $pdo->query("SELECT * FROM payroll_payslips WHERE emp_code='0025'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
