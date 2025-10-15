<?php
declare(strict_types=1);

namespace App\Http;

/**
 * Minimal in-memory stream compatible with PSR-7 concepts.
 * This is NOT a full implementation of Psr\Http\Message\StreamInterface,
 * but provides similar API needed for this project.
 */
class Stream
{
    private string $contents;

    public function __construct(string $contents = '')
    {
        $this->contents = $contents;
    }

    public function __toString(): string
    {
        return $this->contents;
    }

    public function getContents(): string
    {
        return $this->contents;
    }

    public function withContents(string $contents): self
    {
        $clone = clone $this;
        $clone->contents = $contents;
        return $clone;
    }
}
