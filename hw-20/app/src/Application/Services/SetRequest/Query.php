<?php

declare(strict_types=1);

namespace App\Application\Services\SetRequest;

final readonly class Query
{
    public function __construct(public string $data)
    {
    }
}
