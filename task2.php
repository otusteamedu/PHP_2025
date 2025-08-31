<?php
class Solution {
    function fractionToDecimal($numerator, $denominator) {
        if ($numerator == 0) {
            return '0';
        }

        $minus1 = $numerator > 0 && $denominator < 0; 
        $minus2 = $numerator < 0 && $denominator > 0; 
        $minus = $minus1 || $minus2;

        $n = abs($numerator);
        $d = abs($denominator);
        $int = intdiv($n, $d);
        $reminder = $n - $d * $int;
        $result = (string) $int;
        if ($reminder != 0) {
            $result .= '.';
        }
        if ($minus) {
            $result = '-' . $result;
        }

        $reminders = [];
        $res = [];
        $repeat = false;
        $position = false;
        while ($reminder != 0 && !$repeat) {
            $reminders[$reminder] = count($res);
            $num = $reminder * 10;
            $int = intdiv($num, $d);
            $res[] = $int;
            $reminder = $num - $d * $int;
            if (isset($reminders[$reminder])) {
                $position = $reminders[$reminder];
                $repeat = true;
            }
        }

        if ($position === false) {
            $result .= implode('', $res);
            return $result;
        }

        $len = count($res);
        foreach ($res as $key => $item) {
            if ($key == $position) {
                $result .= '(';
            }
            $result .= (string) $item;
            if ($key == $len - 1) {
                $result .= ')';
            }
        }

        return $result;
    }
}