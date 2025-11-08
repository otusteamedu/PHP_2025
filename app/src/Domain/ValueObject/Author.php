<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Embeddable]
class Author
{
    #[ORM\Column(name: 'author', type: Types::STRING, length: 255)]
    private string $value;

    public function __construct(string $value)
    {
        $this->assertValidAuthor($value);
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function assertValidAuthor(string $value): void
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Empty author.');
        }

        if (mb_strlen($value) > 255) {
            throw new InvalidArgumentException('The length of the author must be no more than 255 characters.');
        }
    }
}
