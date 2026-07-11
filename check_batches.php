<?php
require 'includes/db.php';
$stmt = $pdo->query("SELECT * FROM payroll_batches ORDER BY id DESC LIMIT 5");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);
