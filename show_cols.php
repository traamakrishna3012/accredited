<?php
require 'includes/db.php';
$stmt = $pdo->query("SHOW COLUMNS FROM payroll_employees");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
