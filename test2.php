<?php
require 'includes/SimpleXLSX.php';
$xlsx = Shuchkin\SimpleXLSX::parse('Salary_Slip_June_2026.xlsx');
echo count($xlsx->rows(0));
