<?php
declare(strict_types=1);

namespace App\Application\Service;

class BankStatementService
{
    public function generateStatement(string $dateFrom, string $dateTo): string
    {
        $lines = [
            'Bank Statement',
            "{$dateFrom} - {$dateTo}",
            '+300.00 $'
        ];

        return implode(PHP_EOL, $lines);
    }
}
