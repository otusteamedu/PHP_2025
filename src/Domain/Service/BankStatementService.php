<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\BankStatementRequest;
use App\Domain\ValueObject\DateRange;

final class BankStatementService
{
    /**
     * Генерирует упрощенную банковскую выписку для указанного запроса
     */
    public function generateStatement(BankStatementRequest $request): string
    {
        // имитация долгой обработки
        sleep(random_int(2, 5));

        $dateRange = $request->dateRange();
        $daysCount = $dateRange->daysCount();

        // генерируем фейковые данные выписки
        $totalIncome = $this->generateMockIncome($daysCount);
        $totalExpense = $this->generateMockExpense($daysCount);
        $balance = $totalIncome - $totalExpense;

        return $this->formatStatement($dateRange, $totalIncome, $totalExpense, $balance);
    }

    /**
     * Генерирует фейковые доходы за указанное количество дней
     */
    private function generateMockIncome(int $daysCount): float
    {
        $totalIncome = 0;

        for ($day = 0; $day < $daysCount; $day++) {
            // 30% вероятность дохода в день
            if (random_int(1, 100) <= 30) {
                $totalIncome += random_int(1000, 50000);
            }
        }

        return $totalIncome;
    }

    /**
     * Генерирует фейковые расходы за указанное количество дней
     */
    private function generateMockExpense(int $daysCount): float
    {
        $totalExpense = 0;

        for ($day = 0; $day < $daysCount; $day++) {
            // 70% вероятность расхода в день
            if (random_int(1, 100) <= 70) {
                $totalExpense += random_int(500, 15000);
            }
        }

        return $totalExpense;
    }

    /**
     * Форматирует выписку в читаемый вид
     */
    private function formatStatement(
        DateRange $dateRange,
        float $totalIncome,
        float $totalExpense,
        float $balance
    ): string {
        $statement = "БАНКОВСКАЯ ВЫПИСКА\n\n";
        $statement .= "Период: " . $dateRange->format() . "\n";
        $statement .= "Количество дней: " . $dateRange->daysCount() . "\n\n";

        $statement .= "ИТОГО:\n";
        $statement .= "Доходы: " . number_format($totalIncome, 2) . " ₽\n";
        $statement .= "Расходы: " . number_format($totalExpense, 2) . " ₽\n";
        $statement .= "Баланс: " . number_format($balance, 2) . " ₽\n";

        return $statement;
    }
}
