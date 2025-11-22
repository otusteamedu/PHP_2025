<?php

namespace Alisaselezneva\Code\Domain\Services;

class StringProcessor
{
    public function validate(string $input): void
    {
        if (!$this->validateNotEmpty($input)) {
            throw new \InvalidArgumentException('Строка не может быть пустой');
        }

        if (!$this->validateBrackets($input)) {
            throw new \InvalidArgumentException('Некорректное количество скобок или их расположение');
        }
    }

    public function validateNotEmpty(string $input): bool
    {
        return !empty(trim($input));
    }

    public function validateBrackets(string $input): bool
    {
        $stack = [];
        $length = strlen($input);

        for ($i = 0; $i < $length; $i++) {
            $char = $input[$i];

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