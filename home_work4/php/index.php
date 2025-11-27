<?php
// Функция для проверки корректности скобочной последовательности
function checkBrackets(string $string): bool {

    if (empty($string)) {
        return false;
    }

    $count = 0;
    for ($i = 0; $i < strlen($string); $i++) {
        $char = $string[$i];

        if ($char === '(') {
            $count++;
        } elseif ($char === ')') {
            $count--;
        } else {
            continue; 
        }

        if ($count < 0) {
            return false;
        }
    }

    return $count === 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['string'])) {
    $inputString = $_POST['string'];

    if (checkBrackets($inputString)) {
        http_response_code(200);
        echo "Строка со скобками корректна.\n";
    } else {
        http_response_code(400);
        echo "Строка со скобками некорректна или пуста.\n";
    }
} else {
    http_response_code(405);
    echo "Ожидается POST-запрос с параметром 'string'.\n";
}
?>