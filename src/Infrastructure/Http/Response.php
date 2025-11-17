<?php
declare(strict_types=1);

namespace App\Infrastructure\Http;

class Response
{

    private int $statusCode;
    private array $arHeaders = [];
    private Stream $body;

    public function __construct(int $statusCode = 200, array $arHeaders = [], ?Stream $body = null)
    {
        $this->statusCode = $statusCode;
        foreach ($arHeaders as $name => $value) {
            $this->arHeaders[$this->normalizeHeaderName($name)] = (array)$value;
        }
        $this->body = $body ?? new Stream('');
    }

    public function withStatus(int $code): self
    {
        $clone = clone $this;
        $clone->statusCode = $code;

        return $clone;
    }

    public function withHeader(string $name, string|array $value): self
    {
        $clone = clone $this;
        $clone->arHeaders[$this->normalizeHeaderName($name)] = (array)$value;

        return $clone;
    }

    public function withAddedHeader(string $name, string|array $value): self
    {
        $clone = clone $this;
        $normalized = $this->normalizeHeaderName($name);
        $existing = $clone->arHeaders[$normalized] ?? [];
        $clone->arHeaders[$normalized] = array_values(array_merge($existing, (array)$value));

        return $clone;
    }

    public function withoutHeader(string $name): self
    {
        $clone = clone $this;
        unset($clone->arHeaders[$this->normalizeHeaderName($name)]);

        return $clone;
    }

    public function withBody(Stream $body): self
    {
        $clone = clone $this;
        $clone->body = $body;

        return $clone;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->arHeaders;
    }

    public function getHeader(string $name): array
    {
        return $this->arHeaders[$this->normalizeHeaderName($name)] ?? [];
    }

    public function hasHeader(string $name): bool
    {
        return isset($this->arHeaders[$this->normalizeHeaderName($name)]);
    }

    public function getBody(): Stream
    {
        return $this->body;
    }

    private function normalizeHeaderName(string $name): string
    {
        return strtolower($name);
    }
}