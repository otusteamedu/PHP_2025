<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Embeddable]
class Content
{
    #[ORM\Column(name: 'content', type: Types::STRING, length: 3000)]
    private string $value;

    public function __construct(string $value)
    {
        $this->assertValidContent($value);
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function assertValidContent(string $value): void
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Empty content.');
        }

        if (mb_strlen($value) > 3000) {
            throw new InvalidArgumentException('The length of the content must be no more than 3000 characters.');
        }
    }
}
