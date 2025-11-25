<?php
header('Content-Type: text/plain; charset=utf-8');

// Берём POST-параметр string
$s = $_POST['string'] ?? '';
$s = trim($s);

// Пустая строка — 400
if ($s === '') {
    http_response_code(400);
    echo "Неверный запрос: пустая строка\n";
    exit;
}

// Счётчик открытых скобок
$openCount = 0;

for ($i = 0; $i < strlen($s); $i++) {
    if ($s[$i] === '(') {
        $openCount++;
    } elseif ($s[$i] === ')') {
        $openCount--;
        // Если закрывающая раньше открывающей
        if ($openCount < 0) {
            http_response_code(400);
            echo "Неверный запрос: закрывающая скобка раньше открывающей\n";
            exit;
        }
    } else {
        http_response_code(400);
        echo "Неверный запрос: недопустимый символ\n";
        exit;
    }
}

// Если после обхода не все скобки закрыты — 400
if ($openCount !== 0) {
    http_response_code(400);
    echo "Неверный запрос: несоответствие количества скобок\n";
    exit;
}

// Всё ок — 200
http_response_code(200);
echo "Строка корректна\n";
