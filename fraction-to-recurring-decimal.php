<?php

function fractionToDecimal($numerator, $denominator)
{
    if ($numerator % $denominator === 0)
        return (string)intdiv($numerator, $denominator);

    $arrNum = [];
    $fractionPart = '';
    $minus = ($numerator < 0 && $denominator > 0) || ($numerator > 0 && $denominator < 0)   ? '-' : '';

    function recursion($remainder, $denominator, &$arrNum, &$fractionPart)
    {
        if ($remainder === 0)
            return $fractionPart;

        if (isset($arrNum[$remainder]))
            return
                substr($fractionPart, 0, $arrNum[$remainder]) . '(' .
                substr($fractionPart, $arrNum[$remainder]) . ')';

        $arrNum[$remainder] = strlen($fractionPart);
        $remainder *= 10;
        $fractionPart .= intdiv($remainder, $denominator);
        $remainder %= $denominator;

        return recursion($remainder, $denominator, $arrNum, $fractionPart);
    };


    return $minus .  (string)abs(intdiv($numerator, $denominator)) . '.' . str_replace('-', '', recursion($numerator % $denominator, $denominator, $arrNum, $fractionPart));
}
