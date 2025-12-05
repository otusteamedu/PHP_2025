<?php

declare(strict_types=1);

session_start();
$_SESSION['visits'] = ($_SESSION['visits'] ?? 0) + 1;
$_SESSION['last_visit_at'] = $_SESSION['last_visit_at'] ?? date('c');
$_SESSION['container'] = $_SERVER['HOSTNAME'] ?? '-';

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
            $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
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
echo 'Всё хорошо. Контейнер: ' . ($_SERVER['HOSTNAME'] ?? '-') . PHP_EOL;


echo 'PHPSESSID: ' . (session_id() ?: '-') . PHP_EOL;
echo 'SESSION: ' . json_encode($_SESSION, JSON_UNESCAPED_UNICODE) . PHP_EOL;