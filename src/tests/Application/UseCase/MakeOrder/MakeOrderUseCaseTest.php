<?php

use PHPUnit\Framework\TestCase;
use App\Application\UseCase\MakeOrder\MakeOrderUseCase;
use App\Domain\Entity\Order\Order;

class MakeOrderUseCaseTest extends TestCase
{
    public function testInvokeReturnsCorrectOrderData(): void
    {
        // Создание заглушки для объекта "order"
        $mockOrder = $this->createMock(Order::class);
        
        // Настройка методов заглушки
        $mockOrder->method('getId')->willReturn("123");
        $mockOrder->method('getOrder')->willReturn(["Бургер","Хотдог"]);
        $mockOrder->method('getUser')->willReturn("456");
        $mockOrder->method('getStatus')->willReturn('in work');

        // Создание экземпляра MakeOrderUseCase с заглушкой
        $makeOrderUseCase = new MakeOrderUseCase($mockOrder);
        
        // Вызов метода __invoke
        $result = $makeOrderUseCase();

        // Проверка, что результат возвращает ожидаемые данные
        $expectedResult = [
            "order_id" => "123",
            "order_data" => ["Бургер","Хотдог"],
            "order_user_id" => 456,
            "order_status" => 'in work'
        ];

        $this->assertEquals($expectedResult, $result);
    }

    public function testInvokeHandlesNullOrder(): void
    {
        // Передача null в конструктор
        $this->expectException(\Error::class);
        
        $makeOrderUseCase = new MakeOrderUseCase(null);
        
        // Попытка вызова метода __invoke
        $result = $makeOrderUseCase();
    }
}