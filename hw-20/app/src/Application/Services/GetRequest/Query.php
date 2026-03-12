<?php

declare(strict_types=1);

namespace App\Application\Services\GetRequest;

final readonly class Query
{
    public function __construct(public int $requestId)
    {
    }
}
