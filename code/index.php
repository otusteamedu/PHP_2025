<?php

function isValidBrackets($string) {
    $stack = [];

    for ($i = 0; $i < strlen($string); $i++) {
        if ($string[$i] === '(') {
            array_push($stack, '(');
        } elseif ($string[$i] === ')') {
            if (empty($stack) || array_pop($stack) !== '(') {
                return false;
            }
        }
    }

    return empty($stack);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $inputString = $_POST['string'] ?? '';

        if (empty($inputString)) {
            throw new Exception("Строка пуста.");
        }

        if (!isValidBrackets($inputString)) {
            throw new Exception("Скобки некорректны.");
        }

        http_response_code(200);
        echo "200 OK: Скобки корректны.";
    } catch (Exception $e) {
        http_response_code(400);
        echo "400 Bad Request: " . $e->getMessage();
    }
} else {
    http_response_code(405);
    echo "405 Method Not Allowed: Используйте POST.";
}
