<?php declare(strict_types=1);

namespace App\Mapper;

use App\Entity\User;

class UserIdentityMap
{
    /**
     * @var array<int, User>
     */
    private array $users = [];

    public function has(int $id): bool
    {
        return isset($this->users[$id]);
    }

    public function get(int $id): User
    {
        return $this->users[$id];
    }

    public function add(User $user): void
    {
        if ($user->getId() === null) {
            throw new \InvalidArgumentException("Cannot add user without ID to Identity Map.");
        }

        $this->users[$user->getId()] = $user;
    }
}
