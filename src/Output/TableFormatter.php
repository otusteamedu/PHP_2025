<?php

declare(strict_types=1);

namespace App\Output;

use App\DTO\Book;

/**
 * Форматирует вывод результатов поиска в виде таблицы
 */
class TableFormatter
{
    /**
     * Форматирует массив книг в таблицу
     *
     * @param  Book[]  $books
     */
    public function format(array $books): string
    {
        if (empty($books)) {
            return "Результатов не найдено.\n";
        }

        // Определяем ширину колонок
        $colWidths = $this->calculateColumnWidths($books);

        // Формируем таблицу
        $lines = [];
        $lines[] = $this->createSeparator($colWidths);
        $lines[] = $this->createHeader($colWidths);
        $lines[] = $this->createSeparator($colWidths);

        foreach ($books as $book) {
            $lines[] = $this->createRow($book, $colWidths);
        }

        $lines[] = $this->createSeparator($colWidths);

        return implode("\n", $lines)."\n";
    }

    /**
     * Вычисляет ширину колонок
     *
     * @param  Book[]  $books
     * @return array<int>
     */
    private function calculateColumnWidths(array $books): array
    {
        $widths = [
            mb_strlen('Название'),
            mb_strlen('Категория'),
            mb_strlen('Цена'),
            mb_strlen('Остатки'),
        ];

        foreach ($books as $book) {
            $widths[0] = max($widths[0], mb_strlen($book->title));
            $widths[1] = max($widths[1], mb_strlen($book->category));
            $widths[2] = max($widths[2], mb_strlen((string) $book->price));
            $widths[3] = max($widths[3], mb_strlen((string) $book->totalStock));
        }

        return $widths;
    }

    /**
     * Создает разделитель
     *
     * @param  array<int>  $widths
     */
    private function createSeparator(array $widths): string
    {
        $parts = array_map(fn (int $w) => str_repeat('-', $w + 2), $widths);

        return '+'.implode('+', $parts).'+';
    }

    /**
     * Создает заголовок таблицы
     *
     * @param  array<int>  $widths
     */
    private function createHeader(array $widths): string
    {
        return $this->createRow([
            'Название',
            'Категория',
            'Цена',
            'Остатки',
        ], $widths, false);
    }

    /**
     * Создает строку данных
     *
     * @param  Book|array<string>  $data
     * @param  array<int>  $widths
     */
    private function createRow($data, array $widths, bool $isBook = true): string
    {
        if ($isBook) {
            $values = [
                $data->title,
                $data->category,
                (string) $data->price,
                (string) $data->totalStock,
            ];
        } else {
            $values = $data;
        }

        $cells = [];
        foreach ($values as $i => $value) {
            $cells[] = ' '.$this->mbStrPad($value, $widths[$i]).' ';
        }

        return '|'.implode('|', $cells).'|';
    }

    /**
     * Дополняет строку до нужной ширины
     */
    private function mbStrPad(string $str, int $length, string $pad = ' '): string
    {
        return $str.str_repeat($pad, max(0, $length - mb_strlen($str)));
    }
}
