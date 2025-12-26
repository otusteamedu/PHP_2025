<?php

declare(strict_types=1);

namespace App\Domain;

final class Shop
{
    public function __construct(
        public string $name,
        public int $countBooks,
    ) {
    }
}
