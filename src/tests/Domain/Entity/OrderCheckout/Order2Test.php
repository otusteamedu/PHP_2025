<?php

use PHPUnit\Framework\TestCase;
use App\Domain\Entity\OrderCheckout\OrderCheckout;
use App\Domain\Entity\OrderCheckout\Order;

class Order2Test extends TestCase
{
    private $orderCheckoutMock;
    private $order;

    protected function setUp(): void
    {
        // Создаем мок для OrderCheckout
        $this->orderCheckoutMock = $this->createMock(OrderCheckout::class);

        // Настраиваем поведение метода get_order()
        $this->orderCheckoutMock->method('get_order')->willReturn(['productId' => 1, 'quantity' => 2]);

        // Настраиваем поведение метода get_payway()
        $this->orderCheckoutMock->method('get_payway')->willReturn('Credit Card');

        // Настраиваем поведение метода get_getway()
        $this->orderCheckoutMock->method('get_getway')->willReturn('Courier');

        // Создаем экземпляр Order с моком OrderCheckout
        $this->order = new Order($this->orderCheckoutMock);
    }

    public function testGetOrderReturnsCorrectData()
    {
        $expectedOrder = ['productId' => 1, 'quantity' => 2];
        $this->assertEquals($expectedOrder, $this->order->get_order());
    }

    public function testGetPaywayReturnsCorrectValue()
    {
        $expectedPayway = 'Credit Card';
        $this->assertEquals($expectedPayway, $this->order->get_payway());
    }

    public function testGetGetwayReturnsCorrectValue()
    {
        $expectedGetway = 'Courier';
        $this->assertEquals($expectedGetway, $this->order->get_getway());
    }
}