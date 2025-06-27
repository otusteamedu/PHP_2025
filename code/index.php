<?php

declare(strict_types=1);

$body = file_get_contents('php://input');
$input = json_decode($body, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $value = $input['string'];

    if (empty($value)) {
        http_response_code(422);
        exit('Not empty.');
    }

    if (!isBracketsExist($value)) {
        http_response_code(400);
        exit('Not brackets.');
    }

    http_response_code(200);
}

function isBracketsExist(string $str): bool
{
    $balance = 0;

    for ($i = 0; $i < mb_strlen($str); $i++) {
        $char = mb_substr($str, $i, 1);

        if ($char === '(') {
            $balance++;
        } elseif ($char === ')') {
            $balance--;
            if ($balance < 0) {
                return true;
            }
        }
    }

    return $balance == false;
}
