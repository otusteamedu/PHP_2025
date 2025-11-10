<?php
declare(strict_types=1);

namespace App\Http;

class Response
{
    private int $statusCode;
    /** @var array<string, string[]> */
    private array $headers;
    private Stream $body;

    public function __construct(int $statusCode = 200, array $headers = [], ?Stream $body = null)
    {
        $this->statusCode = $statusCode;
        $this->headers = [];
        foreach ($headers as $name => $value) {
            $this->headers[$this->normalizeHeaderName($name)] = (array)$value;
        }
        $this->body = $body ?? new Stream('');
    }

    // PSR-7 style: immutable modifiers
    public function withStatus(int $code): self
    {
        $clone = clone $this;
        $clone->statusCode = $code;
        return $clone;
    }

    public function withHeader(string $name, string|array $value): self
    {
        $clone = clone $this;
        $clone->headers[$this->normalizeHeaderName($name)] = (array)$value;
        return $clone;
    }

    public function withAddedHeader(string $name, string|array $value): self
    {
        $clone = clone $this;
        $normalized = $this->normalizeHeaderName($name);
        $existing = $clone->headers[$normalized] ?? [];
        $clone->headers[$normalized] = array_values(array_merge($existing, (array)$value));
        return $clone;
    }

    public function withoutHeader(string $name): self
    {
        $clone = clone $this;
        unset($clone->headers[$this->normalizeHeaderName($name)]);
        return $clone;
    }

    public function withBody(Stream $body): self
    {
        $clone = clone $this;
        $clone->body = $body;
        return $clone;
    }

    // Accessors
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /** @return array<string, string[]> */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /** @return string[] */
    public function getHeader(string $name): array
    {
        return $this->headers[$this->normalizeHeaderName($name)] ?? [];
    }

    public function hasHeader(string $name): bool
    {
        return isset($this->headers[$this->normalizeHeaderName($name)]);
    }

    public function getBody(): Stream
    {
        return $this->body;
    }

    // Helpers
    private function normalizeHeaderName(string $name): string
    {
        return strtolower($name);
    }
}