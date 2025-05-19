<?php

use PHPUnit\Framework\TestCase;
use App\Domain\Entity\OrderCheckout\OrderCheckout;
use App\Domain\Entity\OrderCheckout\Order;

class OrderCheckoutTest extends TestCase
{
    private OrderCheckout $orderCheckout;

    protected function setUp(): void
    {
        $this->orderCheckout = new OrderCheckout();
    }

    public function testSetOrderStoresOrderCorrectly()
    {
        $orderData = ['productId' => 1, 'quantity' => 2];
        $this->orderCheckout->set_order($orderData);
        $this->assertEquals($orderData, $this->orderCheckout->get_order());
    }

    public function testSetOrderThrowsExceptionOnEmptyOrder()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Ошибка массива");
        $this->orderCheckout->set_order(null);
    }

    public function testSetPaywayStoresPaywayCorrectly()
    {
        $payway = 'Credit Card';
        $this->orderCheckout->set_payway($payway);
        $this->assertEquals($payway, $this->orderCheckout->get_payway());
    }

    public function testSetPaywayThrowsExceptionOnEmptyPayway()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Ошибка способа оплаты");
        $this->orderCheckout->set_payway('');
    }

    public function testSetGetwayStoresGetwayCorrectly()
    {
        $getway = 'Courier';
        $this->orderCheckout->set_getway($getway);
        $this->assertEquals($getway, $this->orderCheckout->get_getway());
    }

    public function testSetGetwayThrowsExceptionOnEmptyGetway()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Ошибка способа доставки");
        $this->orderCheckout->set_getway(null);
    }

    public function testBuildReturnsOrderInstance()
    {
        $orderData = ['productId' => 1, 'quantity' => 2];
        $this->orderCheckout->set_order($orderData)
            ->set_payway('Credit Card')
            ->set_getway('Courier');
        
        $order = $this->orderCheckout->build();
        $this->assertInstanceOf(Order::class, $order);
    }
}