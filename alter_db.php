<?php
require 'includes/db.php';

try {
    $pdo->exec("ALTER TABLE payroll_employees 
        ADD COLUMN pan_no VARCHAR(50) DEFAULT NULL AFTER doj,
        ADD COLUMN esic_no VARCHAR(50) DEFAULT NULL AFTER pan_no,
        ADD COLUMN pf_no VARCHAR(50) DEFAULT NULL AFTER esic_no,
        ADD COLUMN pf_uan VARCHAR(50) DEFAULT NULL AFTER pf_no,
        ADD COLUMN location VARCHAR(100) DEFAULT NULL AFTER pf_uan");
    echo "Columns added successfully.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Columns already exist.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
