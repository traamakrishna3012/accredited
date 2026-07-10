<?php
require 'includes/db.php';
$stmt = $pdo->query('SHOW COLUMNS FROM payroll_payslip_items');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
