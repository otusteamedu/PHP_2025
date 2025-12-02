<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Domain\ValueObject;

final class Url
{
    public function __construct(
        public string $url,
    )
    {
        if (!filter_var($this->url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException("Incorrect url");
        }
    }

    public function getValue(): string
    {
        return $this->url;
    }

    public function __toString(): string
    {
        return $this->url;
    }
}