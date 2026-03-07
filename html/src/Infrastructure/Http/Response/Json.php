<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Http\Response;

final readonly class Json implements ResponseInterface
{
    /**
     * @param int $statusCode
     * @param array $body
     * @param array $headers
     */
    public function __construct(
        private array $body = [],
        private int $statusCode = 200,
        private array $headers = ['Content-Type' => 'application/json'],
    ) {
    }

    /**
     * @param array $body
     * @param int $statusCode
     *
     * @return self
     */
    public static function create(array $body, int $statusCode = 200): self
    {
        return new self(
            body: $body,
            statusCode: $statusCode,
        );
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header(sprintf('%s: %s', $name, $value));
        }

        echo json_encode($this->body, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
