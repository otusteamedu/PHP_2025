<?php declare(strict_types=1);

namespace App\Forms\Search;

/**
 * Class BookSearch
 * @package App\Forms\Search
 */
class BookSearch
{
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
    public ?int $price_min = null;
    /**
     * @var int|null
     */
    public ?int $price_max = null;
    /**
     * @var string|null
     */
    public ?string $stock_shop = null;
    /**
     * @var int|null
     */
    public ?int $stock_min = null;

    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->title = $params['title'] ?? null;
        $this->category = $params['category'] ?? null;
        $this->price_min = isset($params['price_min']) ? (int)$params['price_min'] : null;
        $this->price_max = isset($params['price_max']) ? (int)$params['price_max'] : null;
        $this->stock_shop = $params['stock_shop'] ?? null;
        $this->stock_min = isset($params['stock_min']) ? (int)$params['stock_min'] : null;
    }
}
