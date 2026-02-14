<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

class Response
{
    private int $statusCode;
    private string $content;
    private array $headers;

    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public static function success(array $data, int $statusCode = 200): self
    {
        $data = [
            'success' => true,
            'data' => $data
        ];

        return new self(json_encode($data), $statusCode, ['Content-Type' => 'application/json', 'Charset' => 'utf-8']);
    }

    public static function error(string $message, int $statusCode = 400): self
    {
        $data = [
            'success' => false,
            'error' => $message
        ];

        return new self(json_encode($data), $statusCode, ['Content-Type' => 'application/json', 'Charset' => 'utf-8']);
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        echo $this->content;
    }
}
