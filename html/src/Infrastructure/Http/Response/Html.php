<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Http\Response;

final readonly class Html implements ResponseInterface
{
    /**
     * @param int $statusCode
     * @param string $body
     * @param array $headers
     */
    public function __construct(
        private string $body = '',
        private int $statusCode = 200,
        private array $headers = ['Content-Type' => 'text/html; charset=utf-8'],
    ) {
    }

    /**
     * @param string $body
     * @param int $statusCode
     *
     * @return self
     */
    public static function create(string $body, int $statusCode = 200): self
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
