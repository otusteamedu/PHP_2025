<?php

declare(strict_types=1);

namespace App\Domain\Service\Text;

/**
 * Интерфейс для сервиса обработки текста.
 */
interface TextProcessingServiceInterface
{
    /**
     * Анализирует текст и возвращает статистику.
     *
     * @param string $text
     * @return array{paragraphs: int, words: int, length: int}
     */
    public function analyze(string $text): array;
}
