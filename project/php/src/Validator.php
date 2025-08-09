<?php

namespace src;

use InvalidCharacterException;

class Validator
{
    /**
     * @throws InvalidCharacterException
     */
    public function isValidBracketString(string $string): array
    {
        $stack = [];
        for ($i = 0; $i < strlen($string); $i++) {
            $char = $string[$i];
            if ($char === '(') {
                $stack[] = $char;
            } elseif ($char === ')') {
                if (empty($stack)) {
                    return ['is_valid' => false, 'message' => 'Invalid bracket sequence'];
                }
                array_pop($stack);
            } else {
                throw new InvalidCharacterException("Invalid character in string");
            }
        }

        return [
            'is_valid' => empty($stack),
            'message' => empty($stack) ? 'The bracket string is valid' : 'Invalid bracket sequence'
        ];
    }
}