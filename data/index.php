<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_string = $_POST['string'] ?? '';

    // Проверка на непустоту
    if (empty($input_string)) {
        http_response_code(400);
        echo "Все плохо! Строка пустая!";
        exit;
    }

    // Проверка на корректность количества скобок
    $stack = [];
    foreach (str_split($input_string) as $char) {
        if ($char === '(') {
            $stack[] = $char;
        } elseif ($char === ')') {
            if (empty($stack)) {
                http_response_code(400);
                echo "Все плохо! Неверная строка!";
                exit;
            }
            array_pop($stack);
        }
    }

    if (empty($stack)) {
        http_response_code(200);
        echo "Ура! Все хорошо!";
    } else {
        http_response_code(400);
        echo "Все плохо! Отсутствует закрывающая скобка!";
    }
} else {
    http_response_code(405);
    echo "Все плохо! Используйте POST запрос";
}
