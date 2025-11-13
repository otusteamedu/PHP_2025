<?php

namespace App\Handler;

use Exception;

class CheckString
{
    /**
     * @throws Exception
     */
    public function process(array $data): string
    {
        try {
            $string = $data['string'] ?? '';
        } catch (Exception) {
            $string = '';
        }

        if (empty($string)) {
            throw new Exception('Invalid body', 400);
        }

        if ($this->checkString($string) === false) {
            throw new Exception('Invalid string', 400);
        }

        return 'The string is correct';
    }

    public function checkString(string $string): bool
    {
        $counter = 0;
        $len = strlen($string);

        for ($i = 0; $i < $len; $i++) {
            if ($string[$i] === '(') {
                $counter++;
            } elseif ($string[$i] === ')') {
                $counter--;

                if ($counter < 0) {
                    return false;
                }
            }
        }

        return $counter === 0;
    }
}
