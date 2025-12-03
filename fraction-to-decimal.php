<?
class Solution {

    /**
     * Сложность алгоритма O(n)
     * Память - O(n)
     * где n - знаменатель (делитель)
     */

    function fractionToDecimal($numerator, $denominator) {
        if (is_int($numerator / $denominator)) {
            return (string)($numerator / $denominator);
        }
        
        $sign = ($numerator < 0 && $denominator > 0) || ($numerator > 0 && $denominator < 0) ? "-" : "";
        $numerator = abs($numerator);
        $denominator = abs($denominator);
        

        $wholePart = intdiv($numerator, $denominator);
        $remainder = $numerator % $denominator;

        $hashTable = [];
        $decimalPart = "";
        $index = 0;

        while($remainder != 0) {
            if (isset($hashTable[$remainder])) {
                $startIndex = $hashTable[$remainder];
                $normal = substr($decimalPart, 0, $startIndex);
                $periodic = substr($decimalPart, $startIndex);
                return $sign . $wholePart . "." . $normal . "(" . $periodic . ")";
            }
            
            $hashTable[$remainder] = $index;
            $remainder *= 10;
            $resultNumber = intdiv($remainder, $denominator);
            $decimalPart .= $resultNumber;
            $remainder %= $denominator;
            
            $index++;
        }
        

        return $sign . $wholePart . "." . $decimalPart;
    }

}