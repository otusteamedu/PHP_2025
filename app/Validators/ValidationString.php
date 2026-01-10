<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Validators;

class ValidationString
{
    public static function checkingBrackets(string $string): bool
    {
        $countOpenBrackets = 0;

        $arSymbols = str_split($string);
        foreach ($arSymbols as $symbols) {
            if ($symbols === '(') {
                $countOpenBrackets++;
            } elseif ($symbols === ')') {
                if ($countOpenBrackets <= 0) {
                    return false;
                }
                $countOpenBrackets--;
            }
        }

        return $countOpenBrackets === 0;
    }
}
