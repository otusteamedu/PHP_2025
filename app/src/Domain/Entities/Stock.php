<?php

namespace Pryaniki\App\Domain\Entities;

class Stock
{
    public function __construct(
        public string $shop,
        public int $stock
    ) {}
}