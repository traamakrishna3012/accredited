<?php
require_once __DIR__ . '/includes/SimpleXLSX.php';

$xlsx = Shuchkin\SimpleXLSX::parse('C:\Users\trama\Downloads\Salary_Slip_June_2026.xlsx');
if ($xlsx) {
    print_r($xlsx->rows(0));
} else {
    echo Shuchkin\SimpleXLSX::parseError();
}
