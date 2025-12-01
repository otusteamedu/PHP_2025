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

    // Вовращаю данные для отправки
    public function getContent(): string
    {
        return json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    // Возвращаем статус-код
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    // Устанавливаю заголовки
    public function sendHeaders(): void
    {
        http_response_code($this->statusCode);
        header('Content-Type: application/json; charset=utf-8');
    }
}
?>