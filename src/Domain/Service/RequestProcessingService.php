<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\Request;
use DateTimeImmutable;

final class RequestProcessingService
{
    /**
     * Обрабатывает запрос (симулирует работу)
     */
    public function processRequest(Request $request): string
    {
        // симулируем обработку запроса
        $processingTime = $this->getProcessingTime($request->priority()->value());

        sleep($processingTime);

        // генерируем результат
        $result = sprintf(
            "Request processed successfully\n" .
            "ID: %s\n" .
            "Data: %s\n" .
            "Priority: %s\n" .
            "Processing time: %d seconds\n" .
            "Processed at: %s",
            $request->id()->value(),
            $request->data(),
            $request->priority()->value(),
            $processingTime,
            new DateTimeImmutable()->format('Y-m-d H:i:s')
        );

        return $result;
    }

    /**
     * Возвращает время обработки в зависимости от приоритета,
     * время обработки в секундах:
     * - high: 1 секунда
     * - normal: 3 секунды
     * - low: 5 секунд
     * - default: 3 секунды
     */
    private function getProcessingTime(string $priority): int
    {
        return match ($priority) {
            'high' => 1,
            'normal' => 3,
            'low' => 5,
            default => 3,
        };
    }
}
