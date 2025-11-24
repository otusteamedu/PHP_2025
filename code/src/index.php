<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Alisaselezneva\Code\Infrastructure\Http\Request;

header('Content-Type: application/json; charset=utf-8');


try {
    $request = new Request();
    $request->handle();
            
    http_response_code(200);
    echo json_encode([
        'container' => $_SERVER['HOSTNAME'],
        'message' => 'Строка корректна'
    ]);

} catch (\InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode([
        'container' => $_SERVER['HOSTNAME'],
        'error' => 'Строка не корректна',
        'message' => $e->getMessage()
    ]);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'container' => $_SERVER['HOSTNAME'],
        'error' => 'Внутренняя ошибка',
        'message' => $e->getMessage()
    ]);
}