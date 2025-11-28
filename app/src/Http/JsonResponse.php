<?php
namespace App\Http;

// Класс для создания HTTP ответа в JSON формате
class JsonResponse
{
    // Данные для отправки
    private $data;

    // HTTP статус
    private int $statusCode;

    // Принимает данные и код статуса
    public function __construct($data, int $statusCode = 200)
    {
        $this->data = $data;
        $this->statusCode = $statusCode;
    }

    // Отправляю ответ с HTTP заголовком, потом вывожу JSON
    public function send(): void
    {
        http_response_code($this->statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
?>