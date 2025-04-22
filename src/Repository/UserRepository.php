<?php

namespace App\Repository;

use App\Entity\User;
use App\Mapper\UserMapper;

class UserRepository
{
    public function __construct(private UserMapper $mapper) {}

    /**
     * @param int $id
     * @return User|null
     */
    public function find(int $id): ?User
    {
        return $this->mapper->fetchById($id);
    }

    /**
     * @param User $user
     * @return void
     */
    public function save(User $user): void
    {
        $this->mapper->save($user);
    }
}
