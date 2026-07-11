<?php
require 'includes/SimpleXLSX.php';
$xlsx = Shuchkin\SimpleXLSX::parse('Salary_Slip_June_2026_converted.xlsx');
$rows = $xlsx->rows(0);
print_r($rows[0]);
print_r($rows[1]);
print_r($rows[2]);
