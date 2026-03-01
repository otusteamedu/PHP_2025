<?php

declare(strict_types=1);

namespace App\Infrastructure\Services;

use App\Domain\Interfaces\ReportGeneratorInterface;

class FakeBankReportGenerator implements ReportGeneratorInterface
{
    public function generate(string $name, string $email, int $year): string
    {
        $transactions = $this->generateFakeTransactions($year);
        $totalIncome = 0;
        $totalExpense = 0;

        foreach ($transactions as $transaction) {
            if ($transaction['amount'] > 0) {
                $totalIncome += $transaction['amount'];
            } else {
                $totalExpense += abs($transaction['amount']);
            }
        }

        $balance = $totalIncome - $totalExpense;

        return $this->renderHtml($name, $email, $year, $transactions, $totalIncome, $totalExpense, $balance);
    }

    private function generateFakeTransactions(int $year): array
    {
        $categories = [
            'income' => ['Зарплата', 'Премия', 'Возврат', 'Перевод от клиента', 'Дивиденды'],
            'expense' => ['Аренда', 'Коммунальные услуги', 'Продукты', 'Транспорт', 'Связь', 'Развлечения', 'Одежда']
        ];

        $transactions = [];

        for ($month = 1; $month <= 12; $month++) {
            $transactions[] = [
                'date' => sprintf('%04d-%02d-10', $year, $month),
                'description' => 'Зарплата',
                'amount' => rand(80000, 150000),
            ];

            $expenseCount = rand(5, 10);
            for ($i = 0; $i < $expenseCount; $i++) {
                $day = rand(1, 28);
                $transactions[] = [
                    'date' => sprintf('%04d-%02d-%02d', $year, $month, $day),
                    'description' => $categories['expense'][array_rand($categories['expense'])],
                    'amount' => -rand(500, 15000),
                ];
            }

            $incomeCount = rand(0, 2);
            for ($i = 0; $i < $incomeCount; $i++) {
                $day = rand(1, 28);
                $transactions[] = [
                    'date' => sprintf('%04d-%02d-%02d', $year, $month, $day),
                    'description' => $categories['income'][array_rand($categories['income'])],
                    'amount' => rand(5000, 50000),
                ];
            }
        }

        usort($transactions, fn($a, $b) => strcmp($a['date'], $b['date']));

        return $transactions;
    }

    private function renderHtml(
        string $name,
        string $email,
        int $year,
        array $transactions,
        float $totalIncome,
        float $totalExpense,
        float $balance
    ): string {
        $transactionsHtml = '';
        foreach ($transactions as $t) {
            $amountClass = $t['amount'] > 0 ? 'income' : 'expense';
            $amountFormatted = number_format($t['amount'], 2, ',', ' ');
            $transactionsHtml .= "<tr>
                <td>{$t['date']}</td>
                <td>{$t['description']}</td>
                <td class=\"{$amountClass}\">{$amountFormatted} ₽</td>
            </tr>";
        }

        $totalIncomeFormatted = number_format($totalIncome, 2, ',', ' ');
        $totalExpenseFormatted = number_format($totalExpense, 2, ',', ' ');
        $balanceFormatted = number_format($balance, 2, ',', ' ');
        $generatedAt = date('d.m.Y H:i:s');

        return <<<HTML
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Банковская выписка за {$year} год</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; color: #333; }
        h1 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
        .info { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .info p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #3498db; color: white; }
        tr:hover { background: #f5f5f5; }
        .income { color: #27ae60; font-weight: bold; }
        .expense { color: #e74c3c; font-weight: bold; }
        .summary { margin-top: 30px; background: #ecf0f1; padding: 20px; border-radius: 8px; }
        .summary h3 { margin-top: 0; }
        .summary-row { display: flex; justify-content: space-between; margin: 10px 0; }
        .balance { font-size: 1.5em; font-weight: bold; color: #2c3e50; }
        .footer { margin-top: 40px; font-size: 12px; color: #7f8c8d; text-align: center; }
    </style>
</head>
<body>
    <h1>🏦 Банковская выписка за {$year} год</h1>
    
    <div class="info">
        <p><strong>Клиент:</strong> {$name}</p>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Период:</strong> 01.01.{$year} - 31.12.{$year}</p>
        <p><strong>Дата формирования:</strong> {$generatedAt}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Дата</th>
                <th>Описание</th>
                <th>Сумма</th>
            </tr>
        </thead>
        <tbody>
            {$transactionsHtml}
        </tbody>
    </table>

    <div class="summary">
        <h3>Итого за период</h3>
        <div class="summary-row">
            <span>Поступления:</span>
            <span class="income">+{$totalIncomeFormatted} ₽</span>
        </div>
        <div class="summary-row">
            <span>Расходы:</span>
            <span class="expense">-{$totalExpenseFormatted} ₽</span>
        </div>
        <hr>
        <div class="summary-row">
            <span>Баланс:</span>
            <span class="balance">{$balanceFormatted} ₽</span>
        </div>
    </div>

    <div class="footer">
        <p>Данный документ сформирован автоматически и носит информационный характер.</p>
        <p>© {$year} Банковская система отчётности</p>
    </div>
</body>
</html>
HTML;
    }
}
