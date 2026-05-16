<?php

declare(strict_types=1);

namespace App\Delivery;

use App\DTO\StatementRequest;
use App\DTO\StatementResponse;

/**
 * Контракт стратегии доставки сформированной выписки.
 */
interface StatementDeliveryStrategyInterface
{
    /**
     * Проверяет, подходит ли стратегия для заявки.
     *
     * @param StatementRequest $request Заявка на выписку.
     * @return bool
     */
    public function supports(StatementRequest $request): bool;

    /**
     * Доставляет сформированную выписку выбранным способом.
     *
     * @param StatementRequest $request Заявка на выписку.
     * @param StatementResponse $response Сформированная выписка.
     */
    public function deliver(StatementRequest $request, StatementResponse $response): void;
}
