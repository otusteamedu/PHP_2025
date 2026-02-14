<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

use App\Domain\Entities\Order;

/**
 * Интерфейс строителя заказа
 * Паттерн: Строитель
 */
interface OrderBuilderInterface
{
    /**
     * Начать создание нового заказа
     */
    public function create(): self;

    /**
     * Добавить продукт в заказ
     */
    public function addProduct(ProductInterface $product): self;

    /**
     * Получить построенный заказ
     */
    public function build(): Order;

    /**
     * Сбросить строителя
     */
    public function reset(): self;
}
