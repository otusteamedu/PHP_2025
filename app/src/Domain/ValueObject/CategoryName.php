<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Embeddable]
class CategoryName
{
    #[ORM\Column(name: 'name', type: Types::STRING, length: 255, unique: true)]
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
        if (empty($value)) {
            throw new InvalidArgumentException('Empty category name.');
        }

        if (mb_strlen($value) > 255) {
            throw new InvalidArgumentException('The length of the category name must be no more than 255 characters.');
        }
    }
}
