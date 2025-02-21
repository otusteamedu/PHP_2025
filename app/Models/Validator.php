<?php

namespace App\Models;

class Validator
{
    public static function validateString($string)
    {
        if (empty($string)) {
            throw new \Exception("Empty String");
        }

        // Проверка, что строка не начинается с закрывающей скобки
        if ($string[0] === ')') {
            throw new \Exception("First - ')'");
        }

        $openCount = substr_count($string, '(');
        $closeCount = substr_count($string, ')');

        if ($openCount !== $closeCount) {
            throw new \Exception("Incorrect String");
        }

        return true;
    }
}
