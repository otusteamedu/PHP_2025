<?php

namespace Aovchinnikova\Hw14\Model;

class ParenthesesValidator
{
    public function isValid(string $string): bool
    {
        $balance = 0;
        for ($i = 0; $i < strlen($string); $i++) {
            if ($string[$i] == '(') {
                $balance++;
            } elseif ($string[$i] == ')') {
                $balance--;
                if ($balance < 0) {
                    return false;
                }
            }
        }
        return $balance === 0;
    }
}