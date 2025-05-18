<?php

namespace Tests\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use App\Domain\Factory\PayWay\PayWayFactoryInterface;
use App\Domain\Factory\GetWay\GetWayFactoryInterface;
use App\Domain\Entity\Order\Order;
use App\Application\UseCase\MakeOrderCheckout\MakeOrderCheckoutUseCase;

class MakeOrderCheckoutUseCaseTest extends TestCase
{
    public function testInvokeCommitsCorrectOrderCheckout(): void
    {
        // Создадим mock объекты для Order, PayWayFactoryInterface, and GetWayFactoryInterface
        $mockOrder = $this->createMock(Order::class);
        $mockPayWayFactory = $this->createMock(PayWayFactoryInterface::class);
        $mockGetWayFactory = $this->createMock(GetWayFactoryInterface::class);

        // Зададим поведение методов
        $mockOrder->method('getOrder')->willReturn(['item' => 'Laptop', 'quantity' => 1]);
        $mockPayWayFactory->method('getPayWay')->willReturn('credit_card');
        $mockGetWayFactory->method('getGetWay')->willReturn('standard_delivery');

        // Создадим объект с mock сосотояниями
        $makeOrderCheckoutUseCase = new MakeOrderCheckoutUseCase($mockOrder, $mockPayWayFactory, $mockGetWayFactory);

        // Вызовем функцию
        $result = $makeOrderCheckoutUseCase();

        // Предположим, что это объект
        $this->assertIsObject($result); 

        // Предположим, что у OrderCheckout есть метод для извлечения собранных данных
        $this->assertEquals('Laptop', $result->get_order()['item']);
        $this->assertEquals('credit_card', $result->get_payway());
        $this->assertEquals('standard_delivery', $result->get_getway());
    }

}