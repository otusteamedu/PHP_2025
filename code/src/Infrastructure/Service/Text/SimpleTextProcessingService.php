<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Text;

use App\Domain\Service\Text\TextProcessingServiceInterface;

/**
 * Простая реализация сервиса для анализа текста.
 */
class SimpleTextProcessingService implements TextProcessingServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function analyze(string $text): array
    {
        // Удаляем лишние пробелы и символы новой строки для точного подсчета
        $trimmedText = trim($text);

        if (empty($trimmedText)) {
            return ['paragraphs' => 0, 'words' => 0, 'length' => 0];
        }

        // Считаем параграфы по переводам строк
        $paragraphs = count(explode("\n", $trimmedText));

        // Считаем слова
        $words = str_word_count($trimmedText);

        // Считаем символы
        $length = mb_strlen($trimmedText, 'UTF-8');

        return [
            'paragraphs' => $paragraphs,
            'words' => $words,
            'length' => $length,
        ];
    }
}
