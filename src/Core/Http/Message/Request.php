<?php

declare(strict_types=1);

namespace App\Core\Http\Message;

use App\Core\Http\Exception\InvalidJsonPayloadException;
use App\Core\Http\Exception\InvalidQueryParamException;
use App\Core\Http\Exception\MissingQueryParamException;
use App\Core\Http\Exception\PayloadReadException;

class Request
{
    public function __construct(
        private ?array $server = null,
        private ?array $payload = null,
        private ?array $formPayload = null,
        private ?array $query = null,
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

        $contentType = $this->server['CONTENT_TYPE'] ?? '';
        if (!str_starts_with($contentType, 'application/json')) {
            throw new InvalidJsonPayloadException(
                'Expected Content-Type: application/json, got: ' . $contentType,
                400,
            );
        }

        $content = file_get_contents('php://input');
        if ($content === false) {
            throw new PayloadReadException('Failed to read request body', 400);
        }

        if ($content === '') {
            $this->payload = [];
            return $this->payload;
        }

        try {
            $this->payload = json_decode($content, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InvalidJsonPayloadException($e->getMessage(), 400);
        }

        return $this->payload;
    }

    public function getFormData(): array
    {
        if ($this->formPayload !== null) {
            return $this->formPayload;
        }

        $this->formPayload = $_POST;

        return $this->formPayload;
    }

    public function getQueryParams(): array
    {
        if ($this->query !== null) {
            return $this->query;
        }

        $this->query = $_GET;

        return $this->query;
    }

    /**
     * @throws MissingQueryParamException
     */
    public function getStringQueryParam(string $param): string
    {
        $params = $this->getQueryParams();

        if (!array_key_exists($param, $params)) {
            throw new MissingQueryParamException(
                $param,
                sprintf("Parameter '%s' does not exist", $param),
            );
        }

        $raw = $params[$param];

        if (!is_string($raw)) {
            throw new InvalidQueryParamException(
                $param,
                sprintf("Parameter '%s' must not be array.", $param),
            );
        }

        return $raw;
    }

    /**
     * @throws MissingQueryParamException
     * @throws InvalidQueryParamException
     */
    public function getIntQueryParam(string $param): int
    {
        $raw = $this->getStringQueryParam($param);

        $int = filter_var($raw, FILTER_VALIDATE_INT);

        if ($int === false) {
            throw new InvalidQueryParamException(
                $param,
                sprintf("Parameter '%s' must be integer.", $param)
            );
        }

        return $int;
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

    /**
     * Аналагичный фабричный метод специально для форм.
     */
    public static function fromFormData(array $formData): self
    {
        return new self(server: [], formPayload: $formData);
    }
}
