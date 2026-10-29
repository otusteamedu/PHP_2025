<?php

declare(strict_types=1);

namespace App\Core\Http\Message;

use App\Core\Http\Exception\InvalidJsonPayloadException;
use App\Core\Http\Exception\InvalidQueryParamException;
use App\Core\Http\Exception\MissingQueryParamException;
use App\Core\Http\Exception\PayloadReadException;
use App\Core\Http\Exception\UnsupportedMediaTypeException;

class Request
{
    private ?array $payload = null;
    private ?array $formPayload = null;
    private ?array $query = null;

    private function __construct(
        private readonly array $server,
    ) {
    }

    public static function fromGlobals(): self
    {
        return new self(server: $_SERVER);
    }

    public static function fromArray(array $payload): self
    {
        $self = new self(server: []);
        $self->payload = $payload;

        return $self;
    }

    public static function fromFormData(array $formData): self
    {
        $self = new self(server: []);
        $self->formPayload = $formData;

        return $self;
    }

    public static function fromQuery(array $query): self
    {
        $self = new self(server: []);
        $self->query = $query;

        return $self;
    }

    public function getRequestMethod(): string
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    public function getRequestPath(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '';
        $path = parse_url($uri, PHP_URL_PATH);

        if (!is_string($path) || $path === '') {
            return '/';
        }

        return $path;
    }

    /**
     * @throws UnsupportedMediaTypeException
     * @throws PayloadReadException
     * @throws InvalidJsonPayloadException
     */
    public function getPayload(): array
    {
        if ($this->payload !== null) {
            return $this->payload;
        }

        $contentType = $this->server['CONTENT_TYPE'] ?? '';

        if (!str_starts_with($contentType, 'application/json')) {
            throw new UnsupportedMediaTypeException(
                'Expected Content-Type: application/json, got: ' . $contentType,
            );
        }

        $content = file_get_contents('php://input');

        if ($content === false) {
            throw new PayloadReadException('Failed to read request body');
        }

        if ($content === '') {
            $this->payload = [];
            return $this->payload;
        }

        try {
            $decoded = json_decode($content, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InvalidJsonPayloadException(
                self::jsonErrorMessage($e),
                previous: $e,
            );
        }

        foreach (array_keys($decoded) as $key) {
            if (!is_string($key)) {
                throw new InvalidJsonPayloadException('Payload must be JSON object with named fields');
            }
        }

        $this->payload = $decoded;

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

    private static function jsonErrorMessage(\JsonException $e): string
    {
        return match ($e->getCode()) {
            JSON_ERROR_SYNTAX => 'Malformed JSON syntax',
            JSON_ERROR_UTF8 => 'Request body must be valid UTF-8 string',
            JSON_ERROR_DEPTH => 'Request body is too deeply nested',
            default => 'Request body is not valid JSON',
        };
    }
}
