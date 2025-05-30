class Solution {
    /**
     * @param Integer $numerator
     * @param Integer $denominator
     * @return String
     */
    function fractionToDecimal($numerator, $denominator) {
        if ($numerator == 0) {
            return "0";
        }
    
        $result = "";
        // Определяем знак
        if (($numerator < 0) ^ ($denominator < 0)) {
            $result .= "-";
        }
    
        $numerator = abs($numerator);
        $denominator = abs($denominator);
    
        // Целая часть
        $integerPart = intdiv($numerator, $denominator);
        $result .= strval($integerPart);
    
        $remainder = $numerator % $denominator;
        if ($remainder == 0) {
            return $result;
        }
    
        $result .= ".";
    
        $remainderMap = array();
        while ($remainder != 0) {
            if (array_key_exists($remainder, $remainderMap)) {
                // Вставляем скобки вокруг повторяющейся части
                $result = substr_replace($result, "(", $remainderMap[$remainder], 0);
                $result .= ")";
                break;
            }
            $remainderMap[$remainder] = strlen($result);
            $remainder *= 10;
            $digit = intdiv($remainder, $denominator);
            $result .= strval($digit);
            $remainder %= $denominator;
        }
    
        return $result;
    }
}
