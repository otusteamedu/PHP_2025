<?php

declare(strict_types=1);

namespace Dinargab\Homework14\Application\DTO;

use Dinargab\Homework14\Domain\Entity\Book;

class BookDTO implements TableRowInterface
{
    public function __construct(
        public readonly string $title,
        public readonly string $sku,
        public readonly string $category,
        public readonly int $price,
        public readonly array $stock
    ) {
    }

    public static function fromBook(Book $book): BookDTO
    {
        return new BookDTO(
            title: $book->getTitle(),
            sku: $book->getSku()->getValue(),
            category: $book->getCategory(),
            price: $book->getPrice(),
            stock: $book->getStock()->toArray(),
        );
    }


    public static function getTableHeaders(): array
    {
        return ['Title', 'SKU', 'Category', 'Price', 'Stock Info'];
    }

    public function toTableRow(): array
    {
        $stockSummary = [];
        foreach ($this->stock as $item) {
            $shop           = $item['shop'];
            $qty            = $item['stock'];
            $stockSummary[] = "$shop: $qty";
        }

        return [
            $this->title,
            $this->sku,
            $this->category,
            $this->price,
            implode(", ", $stockSummary)
        ];
    }
}