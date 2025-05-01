<?php
declare(strict_types=1);


namespace App\User\Domain\Repository;


use App\Shared\Domain\Repository\PaginationResult;
use App\User\Domain\Aggregate\User\User;

interface UserRepositoryInterface
{
    public function add(User $user): void;

    public function get(string $userId): ?User;

    public function getByFilter(UserFilter $filter): ?PaginationResult;

}