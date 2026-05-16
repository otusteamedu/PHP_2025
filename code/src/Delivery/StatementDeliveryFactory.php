<?php

declare(strict_types=1);

namespace App\Delivery;

use App\DTO\StatementRequest;
use RuntimeException;

/**
 * Фабрика выбора подходящих стратегий доставки выписки.
 */
final class StatementDeliveryFactory
{
    /**
     * @param StatementDeliveryStrategyInterface[] $strategies
     */
    public function __construct(private readonly array $strategies)
    {
    }

    /**
     * Возвращает все стратегии доставки, подходящие для заявки.
     *
     * @param StatementRequest $request Заявка на выписку.
     * @return StatementDeliveryStrategyInterface[]
     */
    public function create(StatementRequest $request): array
    {
        $matchedStrategies = [];

        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($request)) {
                $matchedStrategies[] = $strategy;
            }
        }

        if ($matchedStrategies === []) {
            throw new RuntimeException('Delivery strategy not found.');
        }

        return $matchedStrategies;
    }
}
