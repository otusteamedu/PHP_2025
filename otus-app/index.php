<?php

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo "Проверка подключений" . "<br>";
    echo $_SERVER['SERVER_ADDR'] . "<br>" . $_SERVER['HOSTNAME'] . "<br>";

    return;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response('Method not allowed', 400);

    return;
}

try {
    $rawData = file_get_contents('php://input');
    $data = json_decode($rawData, true, 512, JSON_THROW_ON_ERROR);
    $string = $data['string'] ?? '';
} catch (Exception $e) {
    $string = '';
}

if (empty($string)) {
    response('Invalid body', 400);

    return;
}

if (checkString($string) === true) {
    response('The string is correct');

    return;
}

response('Invalid string', 400);

function response(string $message, int $statusCode = 200): void {
    http_response_code($statusCode);
    echo $message;
}

function checkString(string $string): bool {
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