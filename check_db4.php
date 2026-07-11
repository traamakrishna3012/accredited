<?php
require 'includes/db.php';
$stmt = $pdo->query("SELECT * FROM payroll_payslips WHERE employee_id = 21");
$payslips = $stmt->fetchAll(PDO::FETCH_ASSOC);

$res = [];
foreach ($payslips as $p) {
    $stmt = $pdo->prepare("SELECT * FROM payroll_payslip_items WHERE payslip_id = ?");
    $stmt->execute([$p['id']]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $p['items'] = $items;
    $res[] = $p;
}
echo json_encode($res, JSON_PRETTY_PRINT);
