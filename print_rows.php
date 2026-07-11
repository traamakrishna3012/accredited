<?php
require 'includes/SimpleXLSX.php';
$xlsx = Shuchkin\SimpleXLSX::parse('Salary_Slip_June_2026.xlsx');
$rows = $xlsx->rows(0);
for($i=10; $i<25; $i++){
    print_r($rows[$i]);
}
