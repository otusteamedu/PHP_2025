<?php

declare(strict_types=1);

namespace App\User\Application\UseCase\Query\GetPagedUsers;

use App\Shared\Domain\Repository\Pager;
use App\User\Application\DTO\User\UserDTO;

readonly class GetPagedUsersQueryResult
{
    /**
     * @param UserDTO[] $users
     */
    public function __construct(public array $users, public Pager $pager)
    {
    }
}
