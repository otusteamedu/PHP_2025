<?php

use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Order\OrderObserver;
use App\Domain\Entity\Order\Order;


class OrderObserverTest extends TestCase
{
    private $orderObserver;
    private $orderMock;

    protected function setUp(): void
    {
        // Здесь мы создаем экземпляр OrderObserver с начальным статусом
        $this->orderObserver = new OrderObserver('Pending');

        // Создаем мок для класса Order
        $this->orderMock = $this->createMock(Order::class);
        
        // Настраиваем метод getUser, getId, и getStatus для возврата заранее определенных значений
        $this->orderMock->method('getUser')->willReturn('John Doe');
        $this->orderMock->method('getId')->willReturn('123');
        $this->orderMock->method('getStatus')->willReturn('In work');
    }

    public function testNotifyDisplaysCorrectMessage()
    {
        // Запускаем метод notify и буферизируем вывод
        ob_start();
        $this->orderObserver->notify($this->orderMock);
        $output = ob_get_clean();

        // Проверяем, что вывод соответствует ожиданиям
        $expectedOutput = "<p>Отправим оповещение Пользователю John Doe следующего содержания: Статус вашего заказа 123 изменился на In work</p>";
        $this->assertEquals($expectedOutput, $output);
    }
}
 

