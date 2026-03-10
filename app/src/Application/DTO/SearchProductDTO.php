<?php
declare(strict_types=1);

namespace Pryaniki\App\Application\DTO;

class SearchProductDTO
{
    public ?string $query = null;
    public ?string $category = null;
    public ?int $price = null;
    public ?int $priceFrom = null;
    public ?int $priceTo = null;
    public ?int $stock = null;
    public ?int $stockFrom = null;
    public ?int $stockTo = null;
    public ?string $shop = null;

    public static function fromArray(array $args): self
    {
        $dto = new self();

        $dto->query = $args['query'] ?? null;
        $dto->category = $args['category'] ?? null;
        $dto->price = isset($args['price']) ? (int)$args['price'] : null;
        $dto->priceFrom = isset($args['price-from']) ? (int)$args['price-from'] : null;
        $dto->priceTo = isset($args['price-to']) ? (int)$args['price-to'] : null;

        $dto->stock = isset($args['stock']) ? (int)$args['stock'] : null;
        $dto->stockFrom = isset($args['stock-from']) ? (int)$args['stock-from'] : null;
        $dto->stockTo = isset($args['stock-to']) ? (int)$args['stock-to'] : null;

        $dto->shop = $args['shop'] ?? null;

        return $dto;
    }
}