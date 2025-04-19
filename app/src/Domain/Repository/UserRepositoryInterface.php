<?php
declare(strict_types=1);


namespace App\Domain\Repository;

use App\Domain\Aggregate\User\User;

interface UserRepositoryInterface
{
    public function add(User $user): void;

    public function get(string $userId): ?User;

}