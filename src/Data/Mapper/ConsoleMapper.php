<?php

declare(strict_types=1);

namespace Dinargab\Homework12\Data\Mapper;

use Dinargab\Homework12\Data\Model\Book;

class ConsoleMapper
{

    public static function geTableHeaders(): array
    {
        return ['Title', "SKU", "Category", "Price", "Stock"];
    }

    public static function bookToConsoleRow(Book $book): array
    {
        return [
            $book->getTitle(),
            $book->getSku(),
            $book->getCategory(),
            $book->getPrice(),
            array_reduce($book->getStock(), function ($accumulator, $item) {
                return $accumulator .= $item["shop"] . ": " . $item["stock"] . "\n";
            }, "")
        ];
    }

    public static function booksArrayToConsoleRows(array $array): array
    {
        $rows = [];
        foreach ($array as $book) {
            if (!is_object($book) || get_class($book) !== Book::class) {
                throw new \InvalidArgumentException("All array elements must be objects of " . Book::class . " class");
            }
            $rows[] = self::bookToConsoleRow($book);
        }
        return $rows;
    }
}