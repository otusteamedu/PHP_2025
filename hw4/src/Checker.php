<?php

namespace Dkopasov\Hw4;

use RuntimeException;

class Checker
{

    public function check(string $string): string
    {

        $string = $_POST['string'];

        if (strlen($string) === 0) {
            http_response_code(400);
            throw new RuntimeException('String cannot be empty.');
        }

        $balance = 0;

        for ($i = 0; $i < strlen($string); $i++) {
            $char = $string[$i];

            if ($char === '(') {
                $balance++;
            } elseif ($char === ')') {
                $balance--;
            }

        }

        // Если закрыли больше, чем открыли — ошибка
        if ($balance < 0 || $balance > 0) {
            http_response_code(400);
            throw new RuntimeException('Balance is not correct');
        }

        http_response_code(200);
        return 'all good';
    }

}