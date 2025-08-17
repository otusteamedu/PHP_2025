<?php

declare(strict_types=1);

namespace App\Infrastructure\Console;

use App\Domain\Models\Book;

final class TableRenderer
{
    /**
     * Рендерит результаты поиска в виде таблицы
     */
    public function renderSearchResults(array $books, int $total, float $took): string
    {
        if (empty($books)) {
            return "Поиск не дал результатов.\n";
        }

        $output = [];
        $output[] = "Найдено книг: {$total} (время поиска: {$took}мс)";
        $output[] = "";

        $output[] = $this->renderTableHeader();
        $output[] = $this->renderTableSeparator();

        foreach ($books as $book) {
            $output[] = $this->renderTableRow($book);
        }

        $output[] = $this->renderTableSeparator();

        return implode("\n", $output) . "\n";
    }

    /**
     * Рендерит заголовок таблицы
     */
    private function renderTableHeader(): string
    {
        return sprintf(
            "%-5s | %-50s | %-20s | %-10s | %-10s",
            "ID",
            "Название",
            "Категория",
            "Цена",
            "Остаток"
        );
    }

    /**
     * Рендерит разделитель таблицы
     */
    private function renderTableSeparator(): string
    {
        return str_repeat("-", 105);
    }

    /**
     * Рендерит строку таблицы для книги
     */
    private function renderTableRow(Book $book): string
    {
        $title = $this->truncateString($book->getTitle(), 48);
        $category = $this->truncateString($book->getCategory(), 18);
        $price = number_format($book->getPrice(), 0, '.', ' ') . ' ₽';
        $stock = $book->getTotalStock();

        return sprintf(
            "%-5s | %-50s | %-20s | %-10s | %-10s",
            $book->getId(),
            $title,
            $category,
            $price,
            $stock
        );
    }

    /**
     * Обрезает строку до указанной длины
     */
    private function truncateString(string $string, int $length): string
    {
        if (mb_strlen($string) <= $length) {
            return $string;
        }

        return mb_substr($string, 0, $length - 3) . '...';
    }

    /**
     * Рендерит сообщение об ошибке
     */
    public function renderError(string $message): string
    {
        return "Ошибка: {$message}\n";
    }

    /**
     * Рендерит информационное сообщение
     */
    public function renderInfo(string $message): string
    {
        return "{$message}\n";
    }
}