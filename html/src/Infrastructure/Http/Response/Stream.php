<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Http\Response;

use Closure;

final readonly class Stream implements ResponseInterface
{
    /**
     * @param Closure $callback
     * @param int $statusCode
     * @param array $headers
     */
    public function __construct(
        private Closure $callback,
        private int $statusCode = 200,
        private array $headers = [],
    ) {
    }

    /**
     * @param Closure $callback
     * @param int $statusCode
     * @param array $headers
     *
     * @return self
     */
    public static function create(Closure $callback, int $statusCode = 200, array $headers = []): self
    {
        return new self(
            callback: $callback,
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

        ob_implicit_flush();

        while (ob_get_level() > 0) {
            ob_end_flush();
        }

        flush();

        ($this->callback)();
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
