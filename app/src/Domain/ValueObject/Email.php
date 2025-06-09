<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Embeddable]
class Email
{
    #[ORM\Column(name: 'email', type: Types::STRING, length: 50)]
    private string $value;

    public function __construct(string $value)
    {
        $this->assertValidEmail($value);
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function assertValidEmail(string $value): void
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Empty email.');
        }

        if (mb_strlen($value) > 50) {
            throw new InvalidArgumentException('The length of the email must be no more than 50 characters.');
        }

        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('Incorrect email: ' . $value);
        }
    }
}
