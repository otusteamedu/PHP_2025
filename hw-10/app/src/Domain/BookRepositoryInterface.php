<?php

declare(strict_types=1);

namespace App\Domain;

interface BookRepositoryInterface
{
    public function searchBooks(?string $name, ?string $category, ?int $maxPrice, int $minPrice, bool $inStock): array;

}
