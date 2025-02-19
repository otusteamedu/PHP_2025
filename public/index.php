<?php

try {
    if (!isset($_POST['string']) || $_POST['string'] == '') {
        throw new RuntimeException('Не передан или передан пустой параметр "string"');
    }

    if (!checkBrackets($_POST['string'])) {
        throw new RuntimeException('Неверное соотношение открытых и закрытых скобок');
    }

    http_response_code(200);
    echo json_encode([
        'string' => $_POST['string'],
        'status' => 'success',
        'message' => 'Строка успешно обработана',
    ]);
} catch (\Exception $exception) {
    http_response_code(400);
    echo json_encode([
        'string' => $_POST['string'],
        'status' => 'error',
        'message' => $exception->getMessage(),
    ]);
}

function checkBrackets($str) {
    // Преобразуем строку в массив символов
    $chars = str_split($str);
    $open = 0;
    $close = 0;

    foreach ($chars as $char) {
        switch ($char) {
            case '(':
                $open++;
                break;
            case ')':
                $close++;
                break;
        }
    }

    $openAndCloseBracketsEqualAmount = $open == $close;
    $correctPairsOfBrackets = preg_match('/(\([^()]*\)|\{[^{}]*\}|<[^<>]*>|\[[^\[\]]*\]|\(.*\))/', $str) === 1;

    return ($openAndCloseBracketsEqualAmount && $correctPairsOfBrackets);
}




