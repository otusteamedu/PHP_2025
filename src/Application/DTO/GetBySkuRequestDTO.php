<?php

declare(strict_types=1);

namespace Dinargab\Homework14\Application\DTO;

class GetBySkuRequestDTO
{
    public function __construct(
        public readonly string $sku,
    ) {
    }
}