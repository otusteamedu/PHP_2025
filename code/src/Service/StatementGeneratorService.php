<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\StatementRequest;
use App\DTO\StatementResponse;

/**
 * Сервис генерации текста банковской выписки.
 */
final class StatementGeneratorService
{
    /**
     * Генерирует стандартный текст выписки по заявке.
     *
     * @param StatementRequest $request Заявка на формирование выписки.
     * @return StatementResponse Результат генерации выписки.
     */
    public function generate(StatementRequest $request): StatementResponse
    {
        $generatedAt = date('Y-m-d H:i:s');
        $subject = 'Банковская выписка по счету ' . $request->accountNumber;

        $body = implode(PHP_EOL, [
            'Здравствуйте!',
            '',
            'Ваша банковская выписка сформирована.',
            '',
            'Счет: ' . $request->accountNumber,
            'Период: ' . $request->dateFrom . ' - ' . $request->dateTo,
            'Дата формирования: ' . $generatedAt,
            '',
            'Операции:',
            $request->dateFrom . ' | Пополнение счета | +15000.00 RUB',
            $request->dateTo . ' | Оплата услуг | -2500.00 RUB',
            '',
            'Итоговое изменение баланса за период: +12500.00 RUB',
        ]);

        return new StatementResponse($subject, $body, $generatedAt);
    }
}
