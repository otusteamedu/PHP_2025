<?php
declare(strict_types=1);

namespace App\Application\Http;

final readonly class Response
{
    private function __construct(
        private int $status = 200,
        private array $headers = [],
        private string $body = ''
    ) {
    }

    public static function json(array $data, int $status = 200, array $headers = []): self
    {
        $headers = array_merge(
            [ 'Content-Type' => 'application/json; charset=utf-8'],
            $headers
        );
        return new self($status, $headers, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public function withHeader(string $name, string $value): self
    {
        $headers = $this->headers;
        $headers[$name] = $value;
        return new self($this->status, $headers, $this->body);
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }
        echo $this->body;
    }
}
