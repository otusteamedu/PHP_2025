<?php

ini_set('session.save_handler', 'redis');
ini_set('session.save_path', 'tcp://redis:6379');

session_start();

function isValidBracketString($string) {
    if (empty($string)) {
        throw new Exception("String is empty");
    }

    $stack = [];
    for ($i = 0; $i < strlen($string); $i++) {
        $char = $string[$i];
        if ($char === '(') {
            $stack[] = $char;
        } elseif ($char === ')') {
            if (empty($stack)) {
                return false;
            }
            array_pop($stack);
        } else {
            throw new Exception("Invalid character in string");
        }
    }
    return empty($stack);
}

// Получаем имя контейнера
$containerName = gethostname();

try {
    $string = $_POST['string'] ?? '';
    if (isValidBracketString($string)) {
        http_response_code(200);
        header("X-Container-Name: $containerName"); // Добавляем заголовок с именем контейнера
        echo "All good! The bracket string is valid. Processed by: $containerName";
    } else {
        http_response_code(400);
        header("X-Container-Name: $containerName");
        echo "Bad request: The bracket string is invalid. Processed by: $containerName";
    }
} catch (Exception $e) {
    http_response_code(400);
    header("X-Container-Name: $containerName");
    echo "Bad request: " . $e->getMessage() . " Processed by: $containerName";
}