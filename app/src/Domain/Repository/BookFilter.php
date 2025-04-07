<?php
declare(strict_types=1);


namespace App\Domain\Repository;


class BookFilter
{
    private ?string $title = null;
    private ?string $category = null;
    private Range $range;
    private ?bool $isInStock = null;

    public function __construct(
        public ?Pager $pager = null,
    )
    {
        $this->range = new Range();
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    public function setMinPrice(int $minPrice): void
    {
        $this->range->min = $minPrice;
    }


    public function setMaxPrice(int $maxPrice): void
    {
        $this->range->max = $maxPrice;
    }

    public function getIsInStock(): ?bool
    {
        return $this->isInStock;
    }

    public function setIsInStock(bool $isInStock): void
    {
        $this->isInStock = $isInStock;
    }

    public function getPager(): ?Pager
    {
        return $this->pager;
    }

    public function getRange(): Range
    {
        return $this->range;
    }

}