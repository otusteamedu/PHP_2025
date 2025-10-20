<?php

function fractionToDecimal($numerator, $denominator) {

    if ($numerator == 0) {
        return "0";
    }
    
    $sign = (($numerator < 0) ^ ($denominator < 0)) ? "-" : "";
    
    $numerator = abs($numerator);
    $denominator = abs($denominator);
    
    $integerPart = intval($numerator / $denominator);
    $remainder = $numerator % $denominator;
    
    if ($remainder == 0) {
        return $sign . $integerPart;
    }
    
    $fractionalPart = "";
    $remainderMap = [];
    
    while ($remainder != 0) {
        if (isset($remainderMap[$remainder])) {
            $pos = $remainderMap[$remainder];
            $nonRepeating = substr($fractionalPart, 0, $pos);
            $repeating = substr($fractionalPart, $pos);
            return $sign . $integerPart . "." . $nonRepeating . "(" . $repeating . ")";
        }
        
        $remainderMap[$remainder] = strlen($fractionalPart);
        
        $remainder *= 10;
        $digit = intval($remainder / $denominator);
        $fractionalPart .= $digit;
        $remainder = $remainder % $denominator;
    }
    
    return $sign . $integerPart . "." . $fractionalPart;
}

echo fractionToDecimal(1, 2) . "\n";  
echo fractionToDecimal(2, 1) . "\n";
echo fractionToDecimal(4, 333) . "\n";    
echo fractionToDecimal(-1, 2) . "\n";  
echo fractionToDecimal(1, -2) . "\n";  
echo fractionToDecimal(-1, -2) . "\n";
echo fractionToDecimal(0, 3) . "\n";
echo fractionToDecimal(22, 7) . "\n";
echo fractionToDecimal(1, 6) . "\n";

?>