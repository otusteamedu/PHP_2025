<?php

declare(strict_types=1);

namespace App\Application\FindUser;

final readonly class FindUserQuery
{
    public function __construct(
        public int $id,
    ){
    }
}
