<?php

namespace Hafiz\Php2025;

class Response {
    public static function json(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit; // Завершаем выполнение скрипта после отправки ответа
    }
}

