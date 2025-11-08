<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShowNews;

readonly class ShowNewsRequest
{
    public function __construct(public int $id)
    {
    }
}
