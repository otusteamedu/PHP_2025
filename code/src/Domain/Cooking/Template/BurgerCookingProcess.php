<?php

declare(strict_types=1);

namespace App\Domain\Cooking\Template;

use App\Domain\Entities\Order;

class BurgerCookingProcess extends AbstractCookingProcess
{
    private const MIN_QUALITY_PRICE = 200.0;

    private const DEFECT_CHANCE = 0.1;

    protected function beforeCooking(Order $order): bool
    {
        $this->log('Бургер: проверка наличия котлеты и булочки');

        if (count($order->getItems()) === 0) {
            $this->log('Бургер: заказ пуст - невозможно готовить');
            return false;
        }

        return true;
    }

    protected function prepareIngredients(Order $order): void
    {
        $this->log('Бургер: разморозка котлеты');
        $this->log('Бургер: нарезка булочки');
        $this->log('Бургер: подготовка соусов');
    }

    protected function doCooking(Order $order): void
    {
        $this->log('Бургер: обжарка котлеты - 3 минуты с каждой стороны');
        $this->log('Бургер: поджаривание булочки');
        $this->log('Бургер: сборка бургера');
        $this->log('Бургер: добавление соусов');
    }

    protected function afterCooking(Order $order): bool
    {
        $this->log('Бургер: проверка прожарки котлеты');
        $this->log('Бургер: проверка температуры');
        $this->log('Бургер: визуальный осмотр');

        $totalPrice = $order->getTotalPrice();
        if ($totalPrice < self::MIN_QUALITY_PRICE) {
            $this->log(sprintf('Бургер: цена %.2f ниже стандарта %.2f', $totalPrice, self::MIN_QUALITY_PRICE));
            return false;
        }

        if (mt_rand(1, 100) <= (self::DEFECT_CHANCE * 100)) {
            $this->log('Бургер: обнаружен дефект - недостаточная прожарка');
            return false;
        }

        $this->log('Бургер: соответствует стандарту качества');
        return true;
    }
}
