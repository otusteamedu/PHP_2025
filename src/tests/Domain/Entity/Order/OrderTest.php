<?php

use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\User;
use App\Domain\ValueObject\Product;
use App\Domain\Entity\Order\OrderObserver;
use App\Domain\Entity\Order\Order;

class OrderTest extends TestCase
{
    private User $user;
    private Product $product;
    private Order $order;

    protected function setUp(): void
    {
        // Создаем моки для User и Product
        $this->user = $this->createMock(User::class);
        $this->product = $this->createMock(Product::class);
         
        // Настраиваем поведение моков
        $this->user->method('getValue')->willReturn("1");
        $this->product->method('getValue')->willReturn(['name' => 'Бургер']);

        // Создаем экземпляр Order
        $this->order = new Order($this->user, $this->product);
    }

    public function testGetIdReturnsSessionId()
    {
        // Проверка, что метод getId возвращает корректный идентификатор сессии
        $this->assertEquals(session_id(), $this->order->getId());
    }

    public function testGetOrderReturnsProductDetails()
    {
        // Проверка, что метод getOrder возвращает данные продукта
        $this->assertEquals(['name' => 'Бургер'], $this->order->getOrder());
    }

    public function testGetUserReturnsUserId()
    {
        // Проверка, что метод getUser возвращает идентификатор пользователя
        $this->assertEquals(1, $this->order->getUser());
    }

    public function testSetStatusChangesOrderStatus()
    {
        // Устанавливаем статус через setStatus
        $this->order->setStatus('Shipped');

        // Проверяем, что статус изменяется
        $this->assertEquals('Shipped', $this->order->getStatus());
    }

    public function testDefaultStatusIsInWork()
    {
        // Проверяем, что по умолчанию статус становится "In work"
        $newOrder = new Order($this->user, $this->product);
        $this->assertEquals('In work', $newOrder->getStatus());
    }

   
}

