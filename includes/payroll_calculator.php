<?php
/**
 * Payroll Calculations and Utilities
 * Accredited Inspection Agency
 */

/**
 * Convert a number to Indian Words (Lakh/Crore)
 */
function number_to_indian_words($number)
{
    $is_negative = false;
    if ($number < 0) {
        $is_negative = true;
        $number = abs($number);
    }
    
    $no = floor($number);
    $point = round($number - $no, 2) * 100;
    
    $digits_1 = strlen($no);
    $i = 0;
    $str = array();
    $words = array(
        '0' => '', '1' => 'One', '2' => 'Two',
        '3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six',
        '7' => 'Seven', '8' => 'Eight', '9' => 'Nine',
        '10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve',
        '13' => 'Thirteen', '14' => 'Fourteen', '15' => 'Fifteen',
        '16' => 'Sixteen', '17' => 'Seventeen', '18' => 'Eighteen',
        '19' => 'Nineteen', '20' => 'Twenty', '30' => 'Thirty',
        '40' => 'Forty', '50' => 'Fifty', '60' => 'Sixty',
        '70' => 'Seventy', '80' => 'Eighty', '90' => 'Ninety'
    );
    $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
    
    while ($i < $digits_1) {
        $divider = ($i == 2) ? 10 : 100;
        $n = floor($no % $divider);
        $no = floor($no / $divider);
        $i += ($divider == 10) ? 1 : 2;
        if ($n) {
            $counter = count($str);
            $str[] = ($n < 21) ? $words[$n] . " " . $digits[$counter]
                : $words[floor($n / 10) * 10] . " " . $words[$n % 10] . " " . $digits[$counter];
        } else {
            $str[] = null;
        }
    }
    
    $str = array_reverse($str);
    $result = implode(' ', array_filter($str));
    $result = trim(str_replace('  ', ' ', $result));

    if ($point > 0) {
        $points = ($point < 21) ? $words[$point] : $words[floor($point / 10) * 10] . " " . $words[$point % 10];
        if ($result != "") {
            $result .= " Rupees and " . trim($points) . " Paise";
        } else {
            $result = trim($points) . " Paise";
        }
    } else {
        $result .= $result == "" ? "Zero Rupees" : " Rupees";
    }

    $final_str = $result . " Only";
    if ($is_negative) {
        $final_str = "Minus " . $final_str;
    }
    return $final_str;
}

/**
 * Calculate prorated actuals, totals, and net pay
 * 
 * @param array $structure Array of items: ['component_id', 'type', 'is_prorated', 'standard_amount']
 * @param float $standard_days Total working days in the month
 * @param float $work_days Actual days worked
 * @return array
 */
function calculate_payslip_totals($structure, $standard_days, $work_days)
{
    $earnings = 0.00;
    $deductions = 0.00;
    $items = [];
    
    // Guard against divide by zero
    $standard_days = max(1, (float)$standard_days);
    $work_days = (float)$work_days;
    
    $proration_factor = $work_days / $standard_days;
    
    foreach ($structure as $comp) {
        $std_amount = (float)$comp['standard_amount'];
        $actual = $std_amount;
        
        if ($comp['is_prorated']) {
            $actual = round($std_amount * $proration_factor, 2);
        }
        
        if ($comp['type'] === 'earning') {
            $earnings += $actual;
        } else {
            $deductions += $actual;
        }
        
        $items[] = [
            'component_id' => $comp['component_id'],
            'standard_amount' => $std_amount,
            'actual_amount' => $actual,
            'type' => $comp['type']
        ];
    }
    
    $net_pay = round($earnings - $deductions, 2);
    
    return [
        'total_earnings' => $earnings,
        'total_deductions' => $deductions,
        'net_pay' => $net_pay,
        'net_pay_words' => number_to_indian_words($net_pay),
        'items' => $items
    ];
}
