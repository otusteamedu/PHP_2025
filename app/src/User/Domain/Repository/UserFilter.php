<?php
declare(strict_types=1);


namespace App\User\Domain\Repository;


use App\Shared\Domain\Repository\Pager;

class UserFilter
{
    public function __construct(
        public ?Pager $pager = null,
    )
    {
    }

}