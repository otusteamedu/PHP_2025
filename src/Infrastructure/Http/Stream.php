<?php
declare(strict_types=1);

namespace App\Infrastructure\Http;

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
