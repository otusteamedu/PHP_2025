class Solution {

    function fractionToDecimal($numerator, $denominator) {
        
        // Обработка нулевого числителя
        if ($numerator == 0) {
            return "0";
        }
        
        $result = "";
        
        // Определение знака
        if (($numerator < 0) ^ ($denominator < 0)) {
            $result .= "-";
        }
        
        // Абсолютные значения
        $num = abs($numerator);
        $den = abs($denominator);
        
        // Целая часть
        $result .= (string)intdiv($num, $den);
        
        // Остаток
        $remainder = $num % $den;
        
        // Если остаток равен нулю, дробной части нет
        if ($remainder == 0) {
            return $result;
        }
        
        // Добавляем десятичную точку
        $result .= ".";
        
        // Хранилище для остатков и их позиций
        $remainderPositions = [];
        $fractional = "";
        $position = 0;
        
        while ($remainder != 0) {

            // Если остаток уже встречался, нашли повторяющуюся последовательность
            if (array_key_exists($remainder, $remainderPositions)) {
                $pos = $remainderPositions[$remainder];
                $repeating = substr($fractional, $pos);
                $nonRepeating = substr($fractional, 0, $pos);
                return $result . $nonRepeating . "(" . $repeating . ")";
            }
            
            // Запоминаем позицию остатка
            $remainderPositions[$remainder] = $position;
            
            // Умножаем остаток на 10 для получения следующей цифры
            $remainder *= 10;
            
            // Добавляем цифру к дробной части
            $fractional .= (string)intdiv($remainder, $den);
            
            // Новый остаток
            $remainder = $remainder % $den;
            
            $position++;
        }
        
        return $result . $fractional;
    }
}