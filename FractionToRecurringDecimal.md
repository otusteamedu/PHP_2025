## Объяснение работы алгоритма:
1) Обработка знака: Если числитель и знаменатель имеют разные знаки, результат отрицательный.
2) Целая часть: Вычисляется простым делением.
3) Если остаток нулевой: Возвращаем целую часть.
4) Дробная часть:
 * Для каждого остатка запоминаем его позицию в результирующей строке
 * Умножаем остаток на 10, делим на знаменатель для получения следующей цифры
 * Если остаток повторяется, вставляем скобки вокруг периодической части

## Сложность:
O(n), где n количество цифр в результате

```php
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
        
        // Работаем с абсолютными значениями
        $numerator = abs($numerator);
        $denominator = abs($denominator);
        
        // Целая часть
        $result .= intval($numerator / $denominator);
        $remainder = $numerator % $denominator;
        
        if ($remainder == 0) {
            return $result;
        }
        
        // Дробная часть
        $result .= ".";
        $remainderMap = [];
        
        while ($remainder != 0) {
            // Если остаток уже встречался, нашли периодическую часть
            if (isset($remainderMap[$remainder])) {
                $result = substr($result, 0, $remainderMap[$remainder]) . "(" . 
                         substr($result, $remainderMap[$remainder]) . ")";
                break;
            }
            
            // Запоминаем позицию текущего остатка
            $remainderMap[$remainder] = strlen($result);
            
            // Умножаем остаток на 10 для получения следующей цифры
            $remainder *= 10;
            $result .= intval($remainder / $denominator);
            $remainder %= $denominator;
        }
        
        return $result;
    }
}
```
