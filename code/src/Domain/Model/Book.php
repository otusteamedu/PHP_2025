<?php

namespace Otus\Code\Domain\Model;

class Book
{    
    public function __construct(
        private string $id,
        private string $title,
        private string $sku,
        private string $category,
        private int $price,
        private array $shops = [],
        private float $score = 0.0
    ) {}
    
    public function getId(): string {
        return $this->id;
    }
    
    public function getTitle(): string {
        return $this->title;
    }
    
    public function getSku(): string {
        return $this->sku;
    }
    
    public function getCategory(): string {
        return $this->category;
    }
    
    public function getPrice(): int {
        return $this->price;
    }
    
    public function getFormattedPrice(): string {
        return number_format($this->price, 0, '', ' ') . ' руб.';
    }
    
    public function getShops(): array {        
        return $this->shops;
    }
    
    public function getScore(): float {
        return $this->score;
    }
    
    public function getTotalStock(): int {
        $total = 0;
        foreach ($this->shops as $shop) {
            $total += $shop->getStock();
        }
        return $total;
    }
    
    public function isAvailable(): bool {
        foreach ($this->shops as $shop) {
            if ($shop->isAvailable()) {
                return true;
            }
        }
        return false;
    }
    
    public static function fromElasticsearchHit(array $hit): self  {
        $source = $hit['_source'];
        $shops = [];
        
        if (!empty($source['stock'])) {
            foreach ($source['stock'] as $shopData) {
                $shops[] = Shop::fromArray($shopData);
            }
        }
        
        return new self(
            $hit['_id'],
            $source['title'] ?? '',
            $source['sku'] ?? '',
            $source['category'] ?? '',
            $source['price'] ?? 0,
            $shops,
            $hit['_score'] ?? 0.0
        );
    }
}