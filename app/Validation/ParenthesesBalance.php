<?php

namespace App\Validation;

class ParenthesesBalance
{
    /**
     * @param string $string
     * @return bool
     */
    public function validate(string $string): bool
    {
        $opening = [')' => '('];

        $parentheses = [];

        foreach (str_split($string) as $char) {
            switch ($char) {
                case '(':
                    $parentheses[] = $char;
                    break;
                case ')':
                    if (!count($parentheses) || array_pop($parentheses) != $opening[$char])
                        return false;
                    break;
                default:
                    break;
            }
        }

        return count($parentheses) === 0;
    }
}