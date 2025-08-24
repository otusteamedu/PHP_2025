<?php

declare(strict_types=1);

namespace App\Application\Service;

final class InputParserService
{
    /**
     * Парсит строку в массив email адресов
     */
    public function parse(string $rawInput): array
    {
        $parts = explode(',', $rawInput);
        $parts = array_map('trim', $parts);

        return array_filter($parts, fn(string $item): bool => $item !== '');
    }
}
