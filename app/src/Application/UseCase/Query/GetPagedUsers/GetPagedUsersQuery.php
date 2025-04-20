<?php
declare(strict_types=1);


namespace App\Application\UseCase\Query\GetPagedUsers;

use App\Application\Query\QueryInterface;
use App\Domain\Repository\UserFilter;

class GetPagedUsersQuery implements QueryInterface
{
    public function __construct(public UserFilter $filter)
    {
    }

}