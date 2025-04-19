<?php

declare(strict_types=1);

namespace App\Application\UseCase\Query\GetPagedUsers;

use App\Application\DTO\User\UserDTO;
use App\Domain\Repository\Pager;

readonly class GetPagedUsersQueryResult
{
    /**
     * @param UserDTO[] $users
     * @param Pager $pager
     */
    public function __construct(public array $users, public Pager $pager)
    {
    }
}
