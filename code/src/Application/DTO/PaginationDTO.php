<?php

declare(strict_types=1);

namespace App\Application\DTO;

class PaginationDTO
{
    public function __construct(
        public readonly array $items,
        public readonly int $total,
        public readonly int $page,
        public readonly int $limit
    ) {
    }
}
