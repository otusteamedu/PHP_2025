<?php

declare(strict_types=1);

namespace App\Domain\Cooking\Template;

use App\Domain\Entities\Order;

class SandwichCookingProcess extends AbstractCookingProcess
{
    private const DEFECT_CHANCE = 0.05; // 5%

    protected function beforeCooking(Order $order): bool
    {
        $this->log('Сэндвич: проверка свежести хлеба');

        if (count($order->getItems()) === 0) {
            $this->log('Сэндвич: заказ пуст - невозможно готовить');
            return false;
        }

        return true;
    }

    protected function prepareIngredients(Order $order): void
    {
        $this->log('Сэндвич: нарезка хлеба');
        $this->log('Сэндвич: нарезка курицы');
        $this->log('Сэндвич: подготовка овощей');
    }

    protected function doCooking(Order $order): void
    {
        $this->log('Сэндвич: обжарка хлеба в тостере');
        $this->log('Сэндвич: нанесение соуса на хлеб');
        $this->log('Сэндвич: укладка начинки слоями');
        $this->log('Сэндвич: закрытие сэндвича и нарезка');
    }

    protected function afterCooking(Order $order): bool
    {
        $this->log('Сэндвич: проверка целостности');
        $this->log('Сэндвич: проверка равномерности начинки');

        if (mt_rand(1, 100) <= (self::DEFECT_CHANCE * 100)) {
            $this->log('Сэндвич: обнаружен дефект - хлеб сгорел');
            return false;
        }

        $this->log('Сэндвич: готов и соответствует стандарту');
        return true;
    }
}
