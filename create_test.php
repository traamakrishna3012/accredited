<?php
require 'includes/SimpleXLSXGen.php';

$data = [
    ['', '', '', '', '', '', ''],
    ['', '', '', '', '', '', ''],
    ['', '', '', '', '', '', ''],
    ['', '', '', '', '', '', ''],
    ['Employee Code', '0025', 'Employee Name', 'SATISH KUMAR PATEL', '', ''],
    ['Bank Name', 'Indian Bank', 'Bank A/C No', '8229856248', '', ''],
    ['DOJ', '15/05/2026', 'PAN', 'EZZPP8320Q', '', ''],
    ['Location', 'SINGARULI', 'ESIC No', 'NA', '', ''],
    ['Designation', 'Sampler', 'Work Days', '31', '', ''],
    ['Standard days', '31', 'LOP Days', '0', '', ''],
    ['Previous Month LOP Days', '0', 'LOP Reversal Days', '0', '', ''],
    ['', '', '', '', '', ''],
    ['Components', 'Standard', 'Earning', 'Components', 'Deductions', 'Amount'],
    ['Basic_ Salary', '10000', '10000', 'Provident Fund', '', '1250'],
    ['HRA', '1500', '1500', 'ESI', '', '200'],
    ['Conveyance', '630', '630', '', '', ''],
    ['Others 1', '1000', '1000', '', '', ''],
    ['Others 2', '1570', '1570', '', '', ''],
];

$xlsx = Shuchkin\SimpleXLSXGen::fromArray($data);
$xlsx->saveAs('test_block.xlsx');
