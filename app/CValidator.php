<?php
declare(strict_types=1);

namespace MyApp;

use MyApp\ValidatorException;

class CValidator
{
    public function __construct() {}

    /**
     * @throws ValidatorException
     */
    public function validate(string $input): bool
    {
        if (empty($input)) {
            throw new ValidatorException('Параметр string не должен быть пустым');
        }

        $balance = 0;
        $len = mb_strlen($input);

        for ($i = 0; $i < $len; $i++) {
            $char = $input[$i];
            if ($char === '(') {
                $balance++;
            } elseif ($char === ')') {
                $balance--;
                if ($balance < 0) {
                    throw new ValidatorException('Неправильная последовательность скобок');
                }
            }
        }

        if ($balance !== 0) {
            throw new ValidatorException('Неравное количество открытых и закрытых скобок');
        }

        return true;
    }
}