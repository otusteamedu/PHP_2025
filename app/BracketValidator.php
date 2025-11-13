<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;

final class BracketValidator
{
    /**
     * Проверяет, что строка содержит корректно сбалансированные скобки
     */
    public function validate(?string $input): bool
    {
        if ($input === null || trim($input) === '') {
            throw new InvalidArgumentException('Параметр string не должен быть пустым');
        }

        $balance = 0;
        $len     = mb_strlen($input);

        for ($i = 0; $i < $len; $i++) {
            $char = $input[$i];
            if ($char === '(') {
                $balance++;
            } elseif ($char === ')') {
                $balance--;
                if ($balance < 0) {
                    throw new InvalidArgumentException('Неправильная последовательность скобок');
                }
            }
        }

        if ($balance !== 0) {
            throw new InvalidArgumentException('Неравное количество открытых и закрытых скобок');
        }

        return true;
    }
}

