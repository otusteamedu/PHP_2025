<?php

declare(strict_types=1);

namespace App\Application\Service;

final class ResultFormatterService
{
    /**
     * Форматирует результат валидации
     */
    public function format(array $results): string
    {
        $lines = [];

        foreach ($results as $email => $isValid) {
            $lines[] = $email . ' => ' . ($isValid ? 'VALID' : 'INVALID');
        }

        return implode("\n", $lines) . "\n";
    }

    /**
     * Форматирует сообщение об ошибке отсутствия параметра
     */
    public function formatUsageHint(): string
    {
        return "Parameter \"emails\" not passed\n";
    }
}
