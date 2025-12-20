<?php
declare(strict_types=1);

namespace App\DTO;

readonly class Book
{
    public string $sku;

    public string $title;

    public string $category;

    public int $price;

    public int $totalStock;

    public float  $score;

    /**
     * @param string $sku
     * @param string $title
     * @param string $category
     * @param int $price
     * @param int $totalStock
     * @param float $score
     */
    public function __construct(
        string $sku,
        string $title,
        string $category,
        int $price,
        int $totalStock,
        float $score
    ) {
        $this->sku = $sku;
        $this->title = $title;
        $this->category = $category;
        $this->price = $price;
        $this->totalStock = $totalStock;
        $this->score = $score;
    }

    /**
     * @param array $hit
     * @return self
     */
    public static function fromElastic(array $hit): self
    {
        $source = $hit['_source'];

        return new self(
            sku: $source['sku'],
            title: $source['title'],
            category: $source['category'],
            price: (int)$source['price'],
            totalStock: array_sum(array_column($source['stock'], 'stock')),
            score: (float)$hit['_score']
        );
    }
}
