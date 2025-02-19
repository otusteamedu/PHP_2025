<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Получаем строку из POST-запроса
    $inputString = $_POST['string'];

    try {
        if (empty($inputString)) {
            throw new Exception("Пустая строка!");
        }

        // Проверка на балансировку скобок
        if (!isValidBrackets($inputString)) {
            throw new Exception("Все плохо!");
        }

        // Ответ 200, если строка корректна
        echo "Все хорошо!";
    } catch (Exception $e) {
        // Ответ 400, если строка некорректна
        http_response_code(400);
        echo $e->getMessage();
    }
}

// Функция для проверки баланса скобок
function isValidBrackets($string) {
    $stack = [];
    $bracketPairs = ['(' => ')'];

    // Проходим по каждому символу строки
    foreach (str_split($string) as $char) {
        if (isset($bracketPairs[$char])) {
            // Если символ - открывающая скобка, добавляем в стек
            array_push($stack, $char);
        } elseif (in_array($char, $bracketPairs)) {
            // Если символ - закрывающая скобка, проверяем стек
            if (empty($stack) || $bracketPairs[array_pop($stack)] !== $char) {
                return false; // Если стек пуст или скобки не соответствуют, возвращаем false
            }
        }
    }

    // Строка валидна, если стек пуст в конце
    return empty($stack);
}
