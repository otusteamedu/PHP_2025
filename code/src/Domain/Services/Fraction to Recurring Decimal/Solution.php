<?php

namespace Alisaselezneva\Code\Domain\Services;

class Solution {

    /**
     * @param int $numerator
     * @param int $denominator
     * @return string
     */
    function fractionToDecimal($numerator, $denominator) {
        if ($numerator === 0) {
            return "0";
        }
        $res = '';
        if (($numerator < 0) xor ($denominator < 0)) {
            $res .= '-';
        }

        $numerator = abs($numerator);
        $denominator = abs($denominator);

        $res .= $this->getPartBeforeComma($numerator, $denominator);
        $fractionPart = $this->getPartAfterComma($numerator * 10, $denominator);

        if ($fractionPart !== '') {
            $res .= '.' . $fractionPart;
        }

        return $res;
    }

    function getPartBeforeComma(&$numerator, $denominator) {
        if ($numerator < $denominator) {
            return 0;
        }
        $result = (int)($numerator / $denominator);
        $numerator = $numerator % $denominator;

        return $result;
    }

    function getPartAfterComma($numerator, $denominator) {
        if ($numerator === 0) {
            return '';
        }

        $result = '';
        $remainderPositions = [];

        while ($numerator !== 0) {
            if (isset($remainderPositions[$numerator])) {
                $start = $remainderPositions[$numerator];
                return substr($result, 0, $start) . '(' . substr($result, $start) . ')';
            }

            $remainderPositions[$numerator] = strlen($result);
            $result .= (string) ((int) ($numerator / $denominator));
            $numerator = ($numerator % $denominator) * 10;
        }

        return $result;
    }
}
