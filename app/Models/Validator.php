<?php

namespace App\Models;

class Validator
{
    public static function validateString($string)
    {
        if (empty($string)) {
            throw new \Exception("Строка пустая");
        }

        // Проверка, что строка не начинается с закрывающей скобки
        if ($string[0] === ')') {
            throw new \Exception("Строка не может начинаться с закрывающей скобки");
        }

        $openCount = substr_count($string, '(');
        $closeCount = substr_count($string, ')');

        if ($openCount !== $closeCount) {
            throw new \Exception("Некорректная строка");
        }

        return true;
    }
}
