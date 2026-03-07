<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Http;

final readonly class Request
{
    /**
     * @param Method $method
     * @param string $uri
     * @param array $queryParams
     * @param array $body
     * @param array $headers
     */
    public function __construct(
        public Method $method,
        public string $uri,
        public array $queryParams = [],
        public array $body = [],
        public array $headers = [],
    ) {
    }

    /**
     * @return self
     */
    public static function fromGlobals(): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $queryParams = $_GET;
        $body = $_POST;
        $headers = getallheaders() ?: [];

        return new self(
            method: Method::from(mb_strtoupper($method)),
            uri: $uri,
            queryParams: $queryParams,
            body: $body,
            headers: $headers,
        );
    }
}
