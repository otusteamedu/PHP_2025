<?php

namespace App;

class StringValidator
{
    /**
     * @param string $string
     * @return bool
     */
    public static function validateExternalSymbols(string $string): bool
    {

        for ($i = 0; $i < strlen($string); $i++) {
            $char = $string[$i];
            if ($char != '(' && $char != ')') {
                return false;
            }
        }
        return true;
    }

    /**
     * @param string $string
     * @return bool
     */
    public static function isStaplesValid(string $string): bool
    {
        $arrayIncoming = str_split($string);
        $staplesOpen = [];

        if (!\App\StringValidator::validateExternalSymbols($string)) {
            throw new \App\Exception\ValidateException('Строка содержит лишние символы');
        }

        for($i = 0; $i < count($arrayIncoming); $i++) {
            if($arrayIncoming[$i] == '(') {
                $staplesOpen[] = $arrayIncoming[$i];
            }

            if ($arrayIncoming[$i] == ')') {
                if (empty($staplesOpen)) {
                    return false;
                } else {
                    array_pop($staplesOpen);
                }
            }
        }

        return empty($staplesOpen);
    }
}