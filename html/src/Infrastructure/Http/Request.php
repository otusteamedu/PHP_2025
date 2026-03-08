<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Http;

use Otus\Queue\Infrastructure\Component\Collection;

final readonly class Request
{
    /**
     * @param Method $method
     * @param string $uri
     * @param Collection $headers
     * @param Collection $queryParams
     * @param Collection $body
     */
    public function __construct(
        public Method $method,
        public string $uri,
        public Collection $headers,
        public Collection $queryParams,
        public Collection $body,
    ) {
    }

    /**
     * @return self
     */
    public static function fromGlobals(): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $headers = Collection::make(getallheaders() ?: []);
        $queryParams = Collection::make($_GET);
        $body = Collection::make(self::getBody($headers));

        return new self(
            method: Method::from(mb_strtoupper($method)),
            uri: $uri,
            headers: $headers,
            queryParams: $queryParams,
            body: $body,
        );
    }

    /**
     * @param Collection $headers
     *
     * @return array
     */
    private static function getBody(Collection $headers): array
    {
        $contentType = $headers->get('Content-Type');

        return match (true) {
            $contentType === 'application/json' => self::getRawBody(),
            default => self::getPostBody(),
        };
    }

    /**
     * @return array
     */
    private static function getRawBody(): array
    {
        $body = file_get_contents('php://input');

        if (is_string($body) && $body !== '') {
            $decodedBody = json_decode($body, true);

            if (is_array($decodedBody)) {
                return $decodedBody;
            }
        }

        return [];
    }

    /**
     * @return array
     */
    private static function getPostBody(): array
    {
        return $_POST;
    }
}
