<?php

namespace Tests\Unit\Infrastructure\Services;

use Domain\Models\Order;
use Infrastructure\Repository\OrderRepository;
use App\Observer\OrderNotifier;
use Infrastructure\Services\OrderService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class OrderServiceTest extends TestCase
{
    private OrderService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $repoMock = $this->createMock(OrderRepository::class);
        $repoMock->method('getOrderList')->willReturn([new Order()]);
        $repoMock->method('findOrder')->willReturn([
            'id' => 1,
            'client_name' => 'Test',
            'client_phone' => '1234567890',
            'order_status' => 'created',
            'product' => 'Burger',
            'price' => 100
        ]);

        $this->service = new OrderService(
            $repoMock,
            $this->createMock(OrderNotifier::class)
        );
    }

    public function testGetList()
    {
        $result = $this->service->getList();
        $this->assertIsArray($result);
        $this->assertInstanceOf(Order::class, $result[0]);
    }

    public function testGetOrder()
    {
        $order = $this->service->getOrder(1);
        $this->assertInstanceOf(Order::class, $order);
    }
}

