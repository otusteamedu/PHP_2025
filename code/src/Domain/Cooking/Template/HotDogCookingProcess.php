<?php

declare(strict_types=1);

namespace App\Domain\Cooking\Template;

use App\Domain\Entities\Order;

class HotDogCookingProcess extends AbstractCookingProcess
{
    private const DEFECT_CHANCE = 0.08; // 8%

    protected function beforeCooking(Order $order): bool
    {
        $this->log('Хот-дог: проверка наличия сосисок');

        if (count($order->getItems()) === 0) {
            $this->log('Хот-дог: заказ пуст - невозможно готовить');
            return false;
        }

        return true;
    }

    protected function prepareIngredients(Order $order): void
    {
        $this->log('Хот-дог: подготовка булочки');
        $this->log('Хот-дог: извлечение сосиски из упаковки');
        $this->log('Хот-дог: подготовка соусов');
    }

    protected function doCooking(Order $order): void
    {
        $this->log('Хот-дог: варка/жарка сосиски - 5 минут');
        $this->log('Хот-дог: подогрев булочки');
        $this->log('Хот-дог: размещение сосиски в булочке');
        $this->log('Хот-дог: добавление соусов по желанию');
    }

    protected function afterCooking(Order $order): bool
    {
        $this->log('Хот-дог: проверка готовности сосиски');
        $this->log('Хот-дог: проверка температуры');

        if (mt_rand(1, 100) <= (self::DEFECT_CHANCE * 100)) {
            $this->log('Хот-дог: обнаружен дефект - сосиска лопнула');
            return false;
        }

        $this->log('Хот-дог: готов и соответствует стандарту');
        return true;
    }
}
