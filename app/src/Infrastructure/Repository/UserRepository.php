<?php
declare(strict_types=1);


namespace App\Infrastructure\Repository;

use App\Domain\Aggregate\User\User;
use App\Domain\Repository\UserRepositoryInterface;
use App\Infrastructure\Database\Db;

class UserRepository implements UserRepositoryInterface
{
    private Db $db;

    public function __construct()
    {
        $this->db = new Db();
    }

    public function add(User $user): void
    {
        $statement =new \PDOStatement();
        var_dump($user);
        die;
    }
}