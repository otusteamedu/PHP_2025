<?php

namespace Arlex2305k\BooksShop\Util;

use Arlex2305k\BooksShop\Entity\Book;

class TableOutput
{
    public static function displayBooks(array $books): void
    {
        if (empty($books)) {
            echo "Книги не найдены.\n";
            return;
        }

        $skuWidth = 10;
        $titleWidth = 30;
        $categoryWidth = 20;
        $priceWidth = 10;
        $stockWidth = 10;

        foreach ($books as $book) {
            $skuWidth = max($skuWidth, self::getStringWidth($book->getSku()));
            $titleWidth = max($titleWidth, self::getStringWidth($book->getTitle()));
            $categoryWidth = max($categoryWidth, self::getStringWidth($book->getCategory()));
            $priceWidth = max($priceWidth, self::getStringWidth(number_format($book->getPrice(), 2)));
            $stockWidth = max($stockWidth, self::getStringWidth((string)$book->getAvailableStock()));
        }

        $skuWidth += 2;
        $titleWidth += 2;
        $categoryWidth += 2;
        $priceWidth += 2;
        $stockWidth += 2;

        self::printSeparator($skuWidth, $titleWidth, $categoryWidth, $priceWidth, $stockWidth);
        self::printRow('SKU', 'Title', 'Category', 'Price', 'Stock', $skuWidth, $titleWidth, $categoryWidth, $priceWidth, $stockWidth);
        self::printSeparator($skuWidth, $titleWidth, $categoryWidth, $priceWidth, $stockWidth);

        foreach ($books as $book) {
            self::printRow(
                $book->getSku(),
                $book->getTitle(),
                $book->getCategory(),
                number_format($book->getPrice(), 2),
                $book->getAvailableStock(),
                $skuWidth,
                $titleWidth,
                $categoryWidth,
                $priceWidth,
                $stockWidth
            );
        }

        self::printSeparator($skuWidth, $titleWidth, $categoryWidth, $priceWidth, $stockWidth);
    }

    private static function getStringWidth(string $str): int
    {
        return mb_strwidth($str, 'UTF-8');
    }

    private static function printSeparator(int $skuWidth, int $titleWidth, int $categoryWidth, int $priceWidth, int $stockWidth): void
    {
        echo '+' . str_repeat('-', $skuWidth) . '+' . 
             str_repeat('-', $titleWidth) . '+' . 
             str_repeat('-', $categoryWidth) . '+' . 
             str_repeat('-', $priceWidth) . '+' . 
             str_repeat('-', $stockWidth) . '+' . "\n";
    }

    private static function printRow(string $sku, string $title, string $category, string $price, string $stock, int $skuWidth, int $titleWidth, int $categoryWidth, int $priceWidth, int $stockWidth): void
    {
        echo '|' . self::padString($sku, $skuWidth) . 
             '|' . self::padString($title, $titleWidth) . 
             '|' . self::padString($category, $categoryWidth) . 
             '|' . self::padString($price, $priceWidth) . 
             '|' . self::padString($stock, $stockWidth) . '|' . "\n";
    }

    private static function padString(string $str, int $width): string
    {
        $strWidth = self::getStringWidth($str);
        if ($strWidth >= $width) {
            return ' ' . mb_substr($str, 0, $width - 3, 'UTF-8') . ' ';
        }
        
        $padding = $width - $strWidth - 2;
        return ' ' . $str . str_repeat(' ', $padding) . ' ';
    }
}
