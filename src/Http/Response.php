<?php declare(strict_types=1);

namespace App\Http;

class Response
{
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
        http_response_code($statusCode);
        $this->data = $data;
        return $this;
    }

    public function __toString(): string
    {
        return $this->json($this->data);
    }
}
