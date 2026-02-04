<?php

namespace Core\Services;

class BracketValidator
{
    public function verify(string $string): bool
    {
        // 1.1. На непустоту
        if (trim($string) === '') {
            return false;
        }

        // 1.2. На корректность кол-ва открытых и закрытых скобок
        $stack = 0;
        for ($i = 0; $i < strlen($string); $i++) {
            if ($string[$i] === '(') $stack++;
            if ($string[$i] === ')') $stack--;
            if ($stack < 0) return false; // Закрывающих больше, чем открытых в моменте
        }

        return $stack === 0;
    }
}
