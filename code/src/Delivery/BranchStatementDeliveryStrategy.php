<?php

declare(strict_types=1);

namespace App\Delivery;

use App\DTO\StatementRequest;
use App\DTO\StatementResponse;

/**
 * Стратегия доставки выписки через получение в отделении банка.
 */
final class BranchStatementDeliveryStrategy implements StatementDeliveryStrategyInterface
{
    /**
     * Проверяет, нужно ли использовать получение выписки в отделении.
     *
     * @param StatementRequest $request Заявка на выписку.
     * @return bool
     */
    public function supports(StatementRequest $request): bool
    {
        return !$request->hasEmail();
    }

    /**
     * Выводит сообщение о получении выписки в отделении.
     *
     * @param StatementRequest $request Заявка на выписку.
     * @param StatementResponse $response Сформированная выписка.
     */
    public function deliver(StatementRequest $request, StatementResponse $response): void
    {
        echo 'Email не указан. Выписку по счету '
            . $request->accountNumber
            . ' можно забрать в ближайшем отделении банка через 3 рабочих дня.'
            . PHP_EOL;
    }
}
