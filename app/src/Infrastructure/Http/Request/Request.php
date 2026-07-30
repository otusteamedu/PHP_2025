<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Request;

use JsonException;

final class Request
{
    /**
     * @var array<string, string>
     */
    private array $attributes;
    public function __construct(
        private readonly string $method,
        private readonly string $path,
        private readonly array  $body,
    ) {
        $this->attributes = [];
    }
    public function setAttribute(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function getAttribute(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * @throws JsonException
     */
    public static function fromGlobals(): self
    {
        $body = [];

        $content = file_get_contents('php://input');

        if ($content !== '') {
            $body = json_decode(
                $content,
                true,
                flags: JSON_THROW_ON_ERROR,
            );

            if (!is_array($body)) {
                throw new JsonException('JSON body must be an object');
            }
        }

        return new self(
            method: $_SERVER['REQUEST_METHOD'],
            path: parse_url(
                $_SERVER['REQUEST_URI'],
                PHP_URL_PATH,
            ),
            body: is_array($body) ? $body : [],
        );
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getJsonBody(): array
    {
        return $this->body;
    }
}
