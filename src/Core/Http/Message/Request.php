<?php

declare(strict_types=1);

namespace App\Core\Http\Message;

use App\Core\Http\Exception\InvalidJsonPayloadException;
use App\Core\Http\Exception\MissingQueryParamException;
use App\Core\Http\Exception\PayloadReadException;

class Request
{
    public function __construct(
        private ?array $server = null,
        private ?array $payload = null,
    ) {
        // Инициализируем server (для реального приложения)
        $this->server ??= $_SERVER;
    }

    public function getRequestMethod(): string
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    public function getRequestPath(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '';
        $path = parse_url($uri, PHP_URL_PATH);

        if ($path === false || $path === '') {
            return '/';
        }

        return $path;
    }

    /**
     * @throws PayloadReadException
     * @throws InvalidJsonPayloadException
     */
    public function getPayload(): ?array
    {
        if ($this->payload !== null) {
            return $this->payload;
        }

        $content = file_get_contents('php://input');
        if ($content === false) {
            throw new PayloadReadException('Failed to read request body', 400);
        }

        if ($content === '') {
            return [];
        }

        try {
            return json_decode($content, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InvalidJsonPayloadException($e->getMessage(), 400);
        }
    }

    /**
     * @throws MissingQueryParamException
     */
    public function getQueryParam(string $param): mixed
    {
        if (!isset($_GET[$param])) {
            throw new MissingQueryParamException("Param '$param' does not exist", 400);
        }

        return $_GET[$param];
    }

    /**
     * Фабрика для тестов: сразу задаёт готовый payload.
     *
     * Передаём пустой массив в server, чтобы полностью исключить недетерминированность,
     * связанную с реальным $_SERVER (REQUEST_URI, REQUEST_METHOD и т.п.).
     * Это гарантирует, что поведение Request в тестах зависит только от переданных данных.
     */
    public static function fromArray(array $payload): self
    {
        return new self(server: [], payload: $payload);
    }
}
