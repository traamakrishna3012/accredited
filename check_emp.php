<?php
require 'includes/db.php';
$stmt = $pdo->query("SELECT * FROM payroll_employees WHERE emp_code LIKE '%25%' OR name LIKE '%Satish%'");
$emps = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($emps, JSON_PRETTY_PRINT);
