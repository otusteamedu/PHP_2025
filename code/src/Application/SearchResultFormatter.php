<?php

namespace Otus\Code\Application;

use Otus\Code\Domain\Model\Book;

class SearchResultFormatter
{
    /**
     * Выводит результаты поиска в консоль
     */
    public static function displayResults(array $searchResults): void
    {
        if (empty($searchResults['hits'])) {
            self::printNoResults();
            return;
        }
        
        $total = $searchResults['total']['value'] ?? count($searchResults['hits']);
        $books = [];
        
        // Преобразуем результаты в объекты Book
        foreach ($searchResults['hits'] as $hit) {
            $books[] = Book::fromElasticsearchHit($hit);
        }
        
        self::printHeader($total);
        
        foreach ($books as $index => $book) {
            self::printBook($book, $index + 1);
        }
        
        self::printFooter();
    }
    
    /**
     * Выводит заголовок с количеством найденных книг
     */
    private static function printHeader(int $total): void
    {
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "РЕЗУЛЬТАТЫ ПОИСКА\n";
        echo str_repeat("-", 80) . "\n";
        echo "Найдено книг: {$total}\n";
        echo str_repeat("-", 80) . "\n\n";
    }
    
    /**
     * Выводит информацию об одной книге
     */
    private static function printBook(Book $book, int $number): void
    {
        echo "Книга #{$number}\n";
        echo str_repeat("-", 60) . "\n";
        
        // Основная информация
        echo "ID: {$book->getId()}\n";
        echo "Артикул: {$book->getSku()}\n";
        echo "Название: {$book->getTitle()}\n";
        echo "Категория: {$book->getCategory()}\n";
        echo "Цена: {$book->getFormattedPrice()}\n";
        
        // Наличие
        self::printStockInfo($book);
        
        // Релевантность
        if ($book->getScore() > 0) {
            echo "Релевантность: " . round($book->getScore(), 4) . "\n";
        }
        
        echo "\n";
    }
    
    /**
     * Выводит информацию о наличии книги в магазинах
     */
    private static function printStockInfo(Book $book): void
    {
        $shops = $book->getShops();
        
        if (empty($shops)) {
            echo "Наличие: Нет в наличии\n";
            return;
        }
        
        $totalStock = $book->getTotalStock();
        
        if ($totalStock > 0) {
            echo "Наличие: {$totalStock} шт.\n";
            foreach ($shops as $shop) {
                $status = $shop->isAvailable() ? "{$shop->getStock()} шт." : "нет";
                echo "  • Магазин \"{$shop->getName()}\": {$status}\n";
            }
        } else {
            echo "Наличие: Нет в наличии\n";
        }
    }
    
    /**
     * Выводит сообщение об отсутствии результатов
     */
    private static function printNoResults(): void
    {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "РЕЗУЛЬТАТЫ ПОИСКА\n";
        echo str_repeat("-", 60) . "\n";
        echo "Ничего не найдено\n";
        echo str_repeat("=", 60) . "\n\n";
    }
    
    /**
     * Выводит подвал
     */
    private static function printFooter(): void
    {
        echo str_repeat("=", 80) . "\n";
        echo "Поиск завершен\n";
        echo str_repeat("=", 80) . "\n\n";
    }
    
    /**
     * Выводит подробную информацию о книге (альтернативный вариант)
     */
    public static function displayDetailedResults(array $searchResults): void
    {
        if (empty($searchResults['hits']['hits'])) {
            self::printNoResults();
            return;
        }
        
        $total = $searchResults['hits']['total']['value'] ?? count($searchResults['hits']['hits']);
        $books = [];
        
        foreach ($searchResults['hits']['hits'] as $hit) {
            $books[] = Book::fromElasticsearchHit($hit);
        }
        
        self::printHeader($total);
        
        foreach ($books as $index => $book) {
            self::printDetailedBook($book, $index + 1);
        }
        
        self::printFooter();
    }
    
    /**
     * Подробный вывод информации о книге
     */
    private static function printDetailedBook(Book $book, int $number): void
    {
        $shops = $book->getShops();
        $available = $book->isAvailable() ? "ДА" : "НЕТ";
        $availabilityIcon = $book->isAvailable() ? "✓" : "✗";
        
        echo "\n";
        echo str_repeat("=", 70) . "\n";
        echo "КНИГА #{$number}\n";
        echo str_repeat("-", 70) . "\n";
        
        echo "Основная информация:\n";
        echo "  ID: {$book->getId()}\n";
        echo "  Артикул: {$book->getSku()}\n";
        echo "  Название: {$book->getTitle()}\n";
        echo "  Категория: {$book->getCategory()}\n";
        echo "  Цена: {$book->getFormattedPrice()}\n";
        
        echo "\nНаличие ({$availabilityIcon} {$available}):\n";
        if (!empty($shops)) {
            echo "  Всего в наличии: {$book->getTotalStock()} шт.\n\n";
            echo "  В магазинах:\n";
            foreach ($shops as $shop) {
                echo "    • Магазин: {$shop->getName()}\n";
                echo "      Количество: {$shop->getStock()} шт.\n";
                echo "      Статус: " . ($shop->isAvailable() ? "В наличии" : "Нет в наличии") . "\n";
                echo "\n";
            }
        } else {
            echo "  Нет информации о наличии\n";
        }
        
        if ($book->getScore() > 0) {
            echo "Релевантность поиска: " . round($book->getScore(), 4) . "\n";
        }
        
        echo str_repeat("=", 70) . "\n";
    }
}