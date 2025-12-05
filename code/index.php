<?php

declare(strict_types=1);

function isValidParentheses(string $s): bool {
    $balance = 0;
    $len = strlen($s);
    for ($i = 0; $i < $len; $i++) {
        $ch = $s[$i];
        if ($ch === '(') {
            $balance++;
        } elseif ($ch === ')') {
            $balance--;
            if ($balance < 0) {
                return false;
            }
        } else {
            continue;
        }
    }
    return $balance === 0;
}

function respondError(int $code, string $message): void {
    http_response_code($code);
    echo $message;
    exit;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

header('Content-Type: text/plain; charset=utf-8');

if ($method === 'POST') {
    if (!array_key_exists('string', $_POST)) {
        respondError(400, 'Отсутствует обязательный параметр string');
    }

    $input = (string)$_POST['string'];

    if (trim($input) === '') {
        respondError(400, 'В параметре string отсутствует значение.');
    }

    if (preg_match('/[()]/', $input)) {
        if (!isValidParentheses($input)) {
            respondError(400, 'Невалидное значение. Количество открытых и закрытых скобок не совпадает.');
        }
    }
}

http_response_code(200);
echo 'Всё хорошо. Контейнер: ' . ($_SERVER['HOSTNAME'] ?? '-');
