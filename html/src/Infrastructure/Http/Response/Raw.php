<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Http\Response;

final readonly class Raw implements ResponseInterface
{
    /**
     * @param int $statusCode
     * @param string $body
     * @param array $headers
     */
    public function __construct(
        private string $body = '',
        private int $statusCode = 200,
        private array $headers = [],
    ) {
    }

    /**
     * @param string $body
     * @param int $statusCode
     * @param array $headers
     *
     * @return self
     */
    public static function create(string $body, int $statusCode = 200, array $headers = []): self
    {
        return new self(
            body: $body,
            statusCode: $statusCode,
            headers: $headers,
        );
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header(sprintf('%s: %s', $name, $value));
        }

        echo $this->body;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
