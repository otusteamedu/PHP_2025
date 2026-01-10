<?php
declare(strict_types=1);


namespace App\Domain\Book;

class Book implements \JsonSerializable
{

    /**
     * @var BookStock[]
     */
    private array $stocks = [];

    public function __construct(
        private string $id,
        private string $title,
        private string $sku,
        private string $category,
        private int    $price,
    )
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function adStock(BookStock $stock): void
    {
        if (!in_array($stock, $this->stocks)) {
            $this->stocks[] = $stock;
        }
    }

    public function getStocks(): array
    {
        return $this->stocks;
    }

    public function inStock(): int
    {
        $count = 0;
        foreach ($this->stocks as $stock) {
            if ($stock->getStock()) {
                $count += $stock->getStock();
            }
        }
        return $count;
    }


    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

}