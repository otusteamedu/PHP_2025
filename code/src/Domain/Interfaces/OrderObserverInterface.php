<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

use App\Domain\Entities\Order;

/**
 * Интерфейс наблюдателя за заказом
 * Паттерн: Наблюдатель
 */
interface OrderObserverInterface
{
    /**
     * Уведомить наблюдателя об изменении заказа
     */
    public function update(Order $order, string $event): void;
}
