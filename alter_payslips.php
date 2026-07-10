<?php
require_once __DIR__ . '/includes/db.php';

try {
    $pdo->exec("ALTER TABLE payroll_payslips ADD COLUMN standard_days DECIMAL(5,2) DEFAULT 0");
    echo "Added standard_days\n";
} catch (Exception $e) {
    echo "standard_days might already exist: " . $e->getMessage() . "\n";
}

try {
    $pdo->exec("ALTER TABLE payroll_payslips ADD COLUMN prev_month_lop DECIMAL(5,2) DEFAULT 0");
    echo "Added prev_month_lop\n";
} catch (Exception $e) {
    echo "prev_month_lop might already exist: " . $e->getMessage() . "\n";
}

try {
    $pdo->exec("ALTER TABLE payroll_payslips ADD COLUMN lop_reversal DECIMAL(5,2) DEFAULT 0");
    echo "Added lop_reversal\n";
} catch (Exception $e) {
    echo "lop_reversal might already exist: " . $e->getMessage() . "\n";
}

echo "Done.";
