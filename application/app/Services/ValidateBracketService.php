<?php

namespace App\Services;

class ValidateBracketService
{
    public function validate(string $brackets, int $pos = 0): bool
    {
        $countBrackets = 0;
        for ($i = 0; $i < mb_strlen($brackets); $i++) {
            $bracket = $brackets[$i];
            if ($bracket === '(') {
                $countBrackets++;
            } elseif ($bracket === ')') {
                $countBrackets--;
                if ($countBrackets < 0) {
                    return false;
                }
            }
        }
        return $countBrackets === 0;
    }
}