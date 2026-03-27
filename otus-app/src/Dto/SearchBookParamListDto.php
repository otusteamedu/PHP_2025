<?php

declare(strict_types=1);

namespace App\Dto;

class SearchBookParamListDto
{
    public function __construct(
        public ?string $query = null,
        public ?int $maxPrice = null,
        public ?int $minPrice = null,
        public ?string $category = null,
    ) {
    }
}
