<?php
require 'includes/db.php';
try {
    $pdo->exec("ALTER TABLE payroll_employees ADD COLUMN standard_days DECIMAL(5,2) DEFAULT 0");
    echo "Added standard_days\n";
} catch (Exception $e) {}
try {
    $pdo->exec("ALTER TABLE payroll_employees ADD COLUMN prev_month_lop DECIMAL(5,2) DEFAULT 0");
    echo "Added prev_month_lop\n";
} catch (Exception $e) {}
try {
    $pdo->exec("ALTER TABLE payroll_employees ADD COLUMN lop_reversal DECIMAL(5,2) DEFAULT 0");
    echo "Added lop_reversal\n";
} catch (Exception $e) {}
echo "Done.";
