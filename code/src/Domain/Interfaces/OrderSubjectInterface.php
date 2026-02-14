<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

use App\Domain\Entities\Order;

/**
 * Интерфейс субъекта наблюдения
 * Паттерн: Наблюдатель
 */
interface OrderSubjectInterface
{
    /**
     * Установить заказ для наблюдения
     */
    public function setOrder(Order $order): void;

    /**
     * Получить текущий заказ (если установлен)
     */
    public function getOrder(): ?Order;

    /**
     * Подписать наблюдателя
     */
    public function attach(OrderObserverInterface $observer): void;

    /**
     * Отписать наблюдателя
     */
    public function detach(OrderObserverInterface $observer): void;

    /**
     * Уведомить всех наблюдателей
     */
    public function notify(string $event): void;
}
