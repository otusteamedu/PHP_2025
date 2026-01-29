<?php

declare(strict_types=1);

namespace Dinargab\Homework14\Infrastructure\Factory;

use Dinargab\Homework14\Application\DTO\BookDTO;
use Dinargab\Homework14\Domain\Entity\Book;
use Dinargab\Homework14\Domain\Factory\BookFactoryInterface;
use Dinargab\Homework14\Domain\ValueObject\Sku;
use Dinargab\Homework14\Domain\ValueObject\StockCollection;
use Dinargab\Homework14\Domain\ValueObject\StockItem;

class BookFactory implements BookFactoryInterface
{

    public function create(string $title, string $sku, string $category, int $price, array $stock): Book
    {
        return new Book(
            $title,
            new Sku($sku),
            $category,
            $price,
            $this->createStockCollectionFromArray($stock)
        );
    }

    public function createFromDTO(BookDTO $bookDTO): Book
    {
        return new Book(
            $bookDTO->title,
            new Sku($bookDTO->sku),
            $bookDTO->category,
            $bookDTO->price,
            $this->createStockCollectionFromArray($bookDTO->stock),
        );
    }

    private function createStockCollectionFromArray(array $stockCollection): StockCollection
    {
        return new StockCollection(
            ...array_map(fn(array $stockItem) => new StockItem($stockItem["shop"], $stockItem["stock"]), $stockCollection)
        );
    }

}