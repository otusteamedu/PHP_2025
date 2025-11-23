<?php
require __DIR__ . '/vendor/autoload.php';

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