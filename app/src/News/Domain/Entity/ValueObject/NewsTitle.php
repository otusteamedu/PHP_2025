<?php

declare(strict_types=1);

namespace App\News\Domain\Entity\ValueObject;

use Webmozart\Assert\Assert;

class NewsTitle implements \Stringable
{
    private string $value;

    public function __construct(string $value)
    {
        $this->assertValidName($value);
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function assertValidName(string $value): void
    {
        Assert::lengthBetween($value, min: 3, max: 255, message: 'News title should be between 3 and 255 characters.');
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
