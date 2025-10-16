<?php

class BracketValidator
{
    /**
     * Проверяет, является ли строка со скобками корректной.
     */
    public function isValid(string $string): bool
    {
        $stack = [];
        $length = strlen($string);

        for ($i = 0; $i < $length; $i++) {
            $char = $string[$i];
            if ($char === '(') {
                array_push($stack, $char);
            } elseif ($char === ')') {
                if (empty($stack)) {
                    return false;
                }
                array_pop($stack);
            }
        }

        return empty($stack);
    }
}
