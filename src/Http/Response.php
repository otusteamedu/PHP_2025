<?php declare(strict_types=1);

namespace App\Http;

class Response
{
    private int $statusCode;
    private array $data;

    public function setHeader(string $header): Response
    {
        header($header);
        return $this;
    }

    public function json(array $data): string
    {
        $this->setHeader('Content-Type: application/json');
        return json_encode($data);
    }

    public function send(int $statusCode, array $data): Response
    {
        $this->statusCode = $statusCode;
        $this->data = $data;
        return $this;
    }

//    public function __toString(): string
//    {
//        return $this->json($this->data);
//    }
    public function __toString(): string
    {
        // Устанавливаем заголовки
        http_response_code($this->statusCode);
        $this->setHeader('Content-Type: application/json');

        try {
            return json_encode($this->data, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            http_response_code(500);
            return json_encode(['error' => 'Failed to encode response']);
        }
    }
}
