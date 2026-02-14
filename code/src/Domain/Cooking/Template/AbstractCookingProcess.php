<?php

declare(strict_types=1);

namespace App\Domain\Cooking\Template;

use App\Domain\Entities\CookingResult;
use App\Domain\Entities\Order;
use App\Domain\Enums\OrderStatus;
use App\Domain\Interfaces\OrderSubjectInterface;

abstract class AbstractCookingProcess
{
    /**
     * @var string[]
     */
    protected array $logs = [];

    public function __construct(
        protected OrderSubjectInterface $orderSubject
    ) {}

    final public function cook(Order $order): CookingResult
    {
        $this->logs = [];
        $this->orderSubject->setOrder($order);

        $this->log('Запуск процесса готовки');
        $preResult = $this->beforeCooking($order);
        if (!$preResult) {
            $this->log('Подготовка не прошла проверку');
            return new CookingResult(
                success: false,
                message: 'Ошибка на этапе подготовки',
                order: $order,
                logs: $this->logs
            );
        }

        $order->setStatus(OrderStatus::PREPARING);
        $this->orderSubject->notify('Статус изменён: подготовка ингредиентов');
        $this->log('Статус: подготовка ингредиентов');

        $this->prepareIngredients($order);
        $this->log('Ингредиенты подготовлены');

        $order->setStatus(OrderStatus::COOKING);
        $this->orderSubject->notify('Статус изменён: готовится на кухне');
        $this->log('Статус: готовится на кухне');

        $this->doCooking($order);
        $this->log('Основной процесс готовки завершён');

        $order->setStatus(OrderStatus::QUALITY_CHECK);
        $this->orderSubject->notify('Статус изменён: проверка качества');
        $this->log('Статус: проверка качества');

        $qualityCheckResult = $this->afterCooking($order);

        if (!$qualityCheckResult) {
            $this->log('Проверка качества НЕ пройдена - утилизация');
            $order->setStatus(OrderStatus::DISPOSED);
            $this->orderSubject->notify('Продукт утилизирован - не прошёл проверку качества');

            return new CookingResult(
                success: false,
                message: 'Продукт не прошёл проверку качества и был утилизирован',
                order: $order,
                logs: $this->logs
            );
        }

        $this->log('Проверка качества пройдена успешно');
        $order->setStatus(OrderStatus::READY);
        $this->orderSubject->notify('Статус изменён: готов к выдаче');
        $this->log('Статус: готов к выдаче');

        return new CookingResult(
            success: true,
            message: 'Заказ успешно приготовлен и готов к выдаче',
            order: $order,
            logs: $this->logs
        );
    }

    protected function beforeCooking(Order $order): bool
    {
        return true;
    }

    protected function prepareIngredients(Order $order): void
    {
        // Базовая реализация - ничего не делаем
    }

    abstract protected function doCooking(Order $order): void;

    protected function afterCooking(Order $order): bool
    {
        return true;
    }

    protected function log(string $message): void
    {
        $this->logs[] = sprintf('[%s] %s', date('H:i:s'), $message);
    }
}
