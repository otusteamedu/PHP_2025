<?php
declare(strict_types=1);

namespace App\Application\Http;

final readonly class Request
{
    private function __construct(
        public RequestMethod $method,
        public string $path,
        public array $query,
        public array $headers,
        public string $rawBody,
        public ?array $json = null,
    ) {
    }

    public static function fromGlobals(): self
    {
        $method = RequestMethod::from(strtoupper($_SERVER['REQUEST_METHOD']));
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = '/' . ltrim(parse_url($uri, PHP_URL_PATH) ?: '/', '/');
        $headers = function_exists('getallheaders') ? (getallheaders() ?: []) : [];
        $rawBody = file_get_contents('php://input') ?: '';
        $json = self::parseJson($rawBody, $headers);

        return new self($method, $path, $_GET, $headers, $rawBody, $json);
    }

    private static function parseJson(string $rawBody, array $headers): ?array
    {
        if ($rawBody === '') {
            return null;
        }
        $contentType = $headers['Content-Type'] ?? $headers['content-type'] ?? '';
        if (str_contains(strtolower($contentType), 'application/json')) {
            $data = json_decode($rawBody, true);
            return is_array($data) ? $data : null;
        }
        return null;
    }
}
