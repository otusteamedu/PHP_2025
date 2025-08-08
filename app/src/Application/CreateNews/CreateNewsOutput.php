<?php

declare(strict_types=1);

namespace App\Application\CreateNews;

class CreateNewsOutput
{
    public function __construct(
        public int $id,
    )
    {
    }
}
