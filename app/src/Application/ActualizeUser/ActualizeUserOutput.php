<?php

declare(strict_types=1);

namespace App\Application\ActualizeUser;

final readonly class ActualizeUserOutput
{
    public function __construct(
        public int $id,
    ){

    }

}