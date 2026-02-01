<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use Classes\DataBase;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(private readonly DataBase $db)
    {
    }

    public function findById(int $id): ?User
    {
        $data = $this->db->select('users', ['*'], ['id' => $id], '', 1);
        return $data ? User::fromState($data[0]) : null;
    }

    public function findByLogin(string $login): ?User
    {
        $data = $this->db->select('users', ['*'], ['login' => $login], '', 1);
        return $data ? User::fromState($data[0]) : null;
    }
}
