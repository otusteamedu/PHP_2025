<?php
declare(strict_types=1);


namespace App\User\Application\UseCase\Query\GetPagedUsers;

use App\Shared\Application\Query\QueryInterface;
use App\User\Domain\Repository\UserFilter;

class GetPagedUsersQuery implements QueryInterface
{
    public function __construct(public UserFilter $filter)
    {
    }

}