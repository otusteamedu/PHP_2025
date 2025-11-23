<?php
require __DIR__ . '/vendor/autoload.php';

/**
 * Проверяет, что в строке для каждой открывающей скобки '(' есть соответствующая закрывающая ')'.
 *
 * @param string $string Строка для проверки.
 * @return bool True, если скобки сбалансированы, иначе false.
 */
function balance(string $string): bool
{
    $balance = 0;

    for ($i = 0, $iMax = strlen($string); $i < $iMax; $i++) {
        $char = $string[$i];

        if ($char === '(') {
            $balance++;
        } elseif ($char === ')') {
            $balance--;
        }

        if ($balance < 0) {
            return false;
        }
    }

    return $balance === 0;
}

$request = htmlspecialchars($_REQUEST['string'] ?? '');


if(!$request){
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => true, 'message' => 'Отсутствуют параметры запроса'], JSON_THROW_ON_ERROR);
    exit();
}


if(!balance($request)){
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => true, 'message' => "Строка '{$request}' НЕ валидна!"], JSON_THROW_ON_ERROR);
    exit();
}


    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => true, 'message' => 'Всё хорошо'], JSON_THROW_ON_ERROR);
    exit();
?>