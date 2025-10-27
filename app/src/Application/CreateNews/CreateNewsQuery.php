<?php

declare(strict_types=1);

namespace App\Application\CreateNews;

class CreateNewsQuery
{
    public function __construct(
        public string $url,
    )
    {
    }
}
