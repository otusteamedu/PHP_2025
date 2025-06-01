<?php

namespace App;

class Validator {
    public function isNotEmpty($string) {
        return !empty($string);
    }

    public function isValidParentheses($string) {
        $stack = [];
        $pairs = ['(' => ')'];

        foreach (str_split($string) as $char) {
            if (isset($pairs[$char])) {
                $stack[] = $char;
            } elseif (in_array($char, $pairs)) {
                if (empty($stack) || $pairs[array_pop($stack)] !== $char) {
                    return false;
                }
            }
        }

        return empty($stack);
    }
}
