<?php

declare(strict_types=1);

namespace App\Application\DTO;

class CreateOrderRequest
{
    /**
     * @param string $productType Тип продукта (burger, sandwich, hotdog)
     * @param string[] $additions Список добавок (lettuce, onion, pepper, cheese, tomato)
     */
    public function __construct(
        public readonly string $productType,
        public readonly array $additions = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            productType: $data['product_type'] ?? '',
            additions: $data['additions'] ?? []
        );
    }
}
