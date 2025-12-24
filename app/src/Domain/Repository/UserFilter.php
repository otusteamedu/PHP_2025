<?php
declare(strict_types=1);


namespace App\Domain\Repository;


class UserFilter
{
    public function __construct(
        public ?Pager $pager = null,
    )
    {
    }

}