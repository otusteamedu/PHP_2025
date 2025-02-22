<?php

namespace app;

class Verification
{
    public static function verificate(string $string): bool
    {
        $left_bracket = 0;
        $right_bracket = 0;
        for ($i = 0; $i < strlen($string); $i++) { 
            if ($string[$i] != '(' && $string[$i] != ')') {
                return false; 
            }

            if ($string[$i] === '(') {
                $left_bracket++;
            }
            if ($string[$i] === ')') {
                $right_bracket++;
            }
        }
        
        return $left_bracket === $right_bracket; 
    }
}