<?php
/**
 * Unit Tests for Payroll Calculations
 */

require_once __DIR__ . '/includes/payroll_calculator.php';

echo "Running Unit Tests for Payroll Calculations...\n\n";

$tests_passed = 0;
$tests_failed = 0;

function assert_test($name, $expected, $actual) {
    global $tests_passed, $tests_failed;
    if ($expected === $actual) {
        echo "[PASS] $name\n";
        $tests_passed++;
    } else {
        echo "[FAIL] $name\n";
        echo "       Expected: $expected\n";
        echo "       Actual:   $actual\n";
        $tests_failed++;
    }
}

// 1. Test Number to Words
assert_test("Words: 0", "Zero Rupees Only", number_to_indian_words(0));
assert_test("Words: 100", "One Hundred Rupees Only", number_to_indian_words(100));
assert_test("Words: 1000", "One Thousand Rupees Only", number_to_indian_words(1000));
assert_test("Words: 100000", "One Lakh Rupees Only", number_to_indian_words(100000));
assert_test("Words: 10000000", "One Crore Rupees Only", number_to_indian_words(10000000));
assert_test("Words: 100005", "One Lakh Five Rupees Only", number_to_indian_words(100005));
assert_test("Words: 1234567", "Twelve Lakh Thirty Four Thousand Five Hundred Sixty Seven Rupees Only", number_to_indian_words(1234567));
assert_test("Words: 15.50", "Fifteen Rupees and Fifty Paise Only", number_to_indian_words(15.50));

// 2. Test Calculation Logic
$sample_structure = [
    ['component_id' => 1, 'type' => 'earning', 'is_prorated' => 1, 'standard_amount' => 10000], // Basic
    ['component_id' => 2, 'type' => 'earning', 'is_prorated' => 1, 'standard_amount' => 5000],  // HRA
    ['component_id' => 6, 'type' => 'deduction', 'is_prorated' => 1, 'standard_amount' => 1200], // PF
    ['component_id' => 8, 'type' => 'deduction', 'is_prorated' => 0, 'standard_amount' => 200],  // PT (Fixed)
];

// Test 2.1: Full Attendance (30 days standard, 30 days worked)
$res_full = calculate_payslip_totals($sample_structure, 30, 30);
assert_test("Calc Full: Earnings", 15000.0, $res_full['total_earnings']);
assert_test("Calc Full: Deductions", 1400.0, $res_full['total_deductions']);
assert_test("Calc Full: Net Pay", 13600.0, $res_full['net_pay']);

// Test 2.2: Half Attendance (30 days standard, 15 days worked)
$res_half = calculate_payslip_totals($sample_structure, 30, 15);
assert_test("Calc Half: Earnings", 7500.0, $res_half['total_earnings']); // 5000 + 2500
assert_test("Calc Half: Deductions", 800.0, $res_half['total_deductions']); // 600 (PF) + 200 (PT, fixed)
assert_test("Calc Half: Net Pay", 6700.0, $res_half['net_pay']);

// Test 2.3: Zero Attendance (30 days standard, 0 days worked)
$res_zero = calculate_payslip_totals($sample_structure, 30, 0);
assert_test("Calc Zero: Earnings", 0.0, $res_zero['total_earnings']);
assert_test("Calc Zero: Deductions", 200.0, $res_zero['total_deductions']); // Fixed PT applies!
assert_test("Calc Zero: Net Pay", -200.0, $res_zero['net_pay']); // Negative net pay is correct for fixed deductions

echo "\nTests Completed: $tests_passed Passed, $tests_failed Failed.\n";
