<?php declare(strict_types=1);

namespace App\DTO;

use App\Entity\User;

readonly class UserDTO
{
    public function __construct(
        public int    $id,
        public string $name,
        public string $email,
    )
    {
    }

    static public function createFromEntity(User $user): self
    {
        return new self(
            $user->getId(),
            $user->getName(),
            $user->getEmail()
        );
    }
}
