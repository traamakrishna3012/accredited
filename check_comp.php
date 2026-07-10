<?php
require 'includes/db.php';
$stmt = $pdo->query('SELECT * FROM payroll_salary_components');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
