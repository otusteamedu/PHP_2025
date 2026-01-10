<?php

namespace App\Repository;

use App\Entity\User;
use App\Mapper\UserMapper;

class UserRepository
{
    private UserMapper $mapper;

    public function __construct(UserMapper $mapper) {
        $this->mapper = $mapper;
    }

    public function findById(int $id): ?User
    {
        return $this->mapper->fetchById($id);
    }

    public function save(User $user): void
    {
        $this->mapper->save($user);
    }
}
