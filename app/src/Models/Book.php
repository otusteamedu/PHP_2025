<?php declare(strict_types=1);

namespace App\Models;

/**
 * Class Book
 * @package App\Models
 */
class Book
{
    /**
     * @var string|null
     */
    public ?string $sku = null;
    /**
     * @var string|null
     */
    public ?string $title = null;
    /**
     * @var string|null
     */
    public ?string $category = null;
    /**
     * @var int|null
     */
    public ?int $price = null;
    /**
     * @var array
     */
    public array $stock = [];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->sku = $attributes['sku'] ?? null;
        $this->title = $attributes['title'] ?? null;
        $this->category = $attributes['category'] ?? null;
        $this->price = isset($attributes['price']) ? (int)$attributes['price'] : null;
        $this->stock = isset($attributes['stock']) ? (array)$attributes['stock'] : [];
    }
}
