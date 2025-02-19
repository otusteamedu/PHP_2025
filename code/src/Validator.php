<?php

namespace Src;

class Validator {
    public static function isValidBrackets(string $string): bool {
        $stack = [];
        for ($i = 0, $len = strlen($string); $i < $len; $i++) {
            if ($string[$i] === '(') {
                array_push($stack, '(');
            } elseif ($string[$i] === ')') {
                if (empty($stack) || array_pop($stack) !== '(') {
                    return false;
                }
            }
        }
        return empty($stack);
    }
}

