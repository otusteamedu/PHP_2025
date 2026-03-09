<?php

declare(strict_types=1);

namespace App\Model;

use App\Enum\BookGenre;
use App\Enum\Bookshop;

class BookshopSearchModel
{
    public function __construct(
        private readonly ?BookGenre $category,
        private readonly ?string $title,
        private readonly ?int $minPrice,
        private readonly ?int $maxPrice,
        private readonly ?Bookshop $shop,
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if ($this->minPrice !== null && $this->maxPrice !== null && $this->minPrice > $this->maxPrice) {
            throw new \InvalidArgumentException('Min price cannot be greater than max price.');
        }

        if ($this->minPrice < 0 || $this->maxPrice < 0) {
            throw new \InvalidArgumentException('Prices cannot be negative.');
        }
    }

    public function getCategory(): ?BookGenre
    {
        return $this->category;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getMinPrice(): ?int
    {
        return $this->minPrice;
    }

    public function getMaxPrice(): ?int
    {
        return $this->maxPrice;
    }

    public function getShop(): ?Bookshop
    {
        return $this->shop;
    }
}
