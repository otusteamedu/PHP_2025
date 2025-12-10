<?php
declare(strict_types=1);

namespace src\Service;

use InvalidArgumentException;

class BracketValidator
{
    public function validate(string $input): void
    {
        if (trim($input) === '') {
            throw new InvalidArgumentException("Строка не должна быть пустой");
        }

        $balance = 0;
        for ($i = 0; $i < strlen($input); $i++) {
            $char = $input[$i];

            if ($char === '(') {
                $balance++;
            } elseif ($char === ')') {
                $balance--;
            }

            if ($balance < 0) {
                throw new InvalidArgumentException("Ошибка: Лишняя закрывающая скобка");
            }
        }

        if ($balance !== 0) {
            throw new InvalidArgumentException("Ошибка: Есть незакрытые скобки");
        }
    }
}
