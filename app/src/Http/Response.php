<?php
declare(strict_types=1);

namespace App\Http;

class Response implements \Stringable
{
    private string $result;
    private int $status;
    private ?array $data;
    private ?string $message;

    public function __construct(int $status, ?array $data = null, ?string $message = null)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        $this->result = $this->getResult($status);
        $this->status = $status;
        $this->data = $data;
        $this->message = $message;
    }

    private function getResult(int $httpStatusCode): string
    {
        return match (true) {
            $httpStatusCode >= 200 && $httpStatusCode < 400 => 'success',
            default => 'error',
        };
    }

    public function __toString(): string
    {
        return json_encode(get_object_vars($this));
    }
}