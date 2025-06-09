<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Embeddable]
class Title
{
    #[ORM\Column(name: 'title', type: Types::STRING, length: 255)]
    private string $value;

    public function __construct(string $value)
    {
        $this->assertValidTitle($value);
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function assertValidTitle(string $value): void
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Empty title.');
        }

        if (mb_strlen($value) > 255) {
            throw new InvalidArgumentException('The length of the title must be no more than 255 characters.');
        }
    }
}
