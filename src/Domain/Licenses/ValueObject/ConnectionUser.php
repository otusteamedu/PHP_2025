<?php
declare(strict_types=1);

namespace Domain\Licenses\ValueObject;

use Domain\Users\Entity\User;

class ConnectionUser
{
    private User $value;

    public function __construct(User $value)
    {
        $this->assertValidName($value);
        $this->value = $value;
    }

    public function getValue(): User
    {
        return $this->value;
    }

    private function assertValidName(User $value): void
    {
        if ($value->getId() === null) {
            throw new \InvalidArgumentException('Error Licenses ConnectionUser');
        }
    }
}