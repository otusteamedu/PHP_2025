<?php

namespace Tests\Functional\Controllers;

use Domain\Models\Order;
use Infrastructure\Adapter\FastFoodItemInterface;
use Infrastructure\Http\Controllers\OrderController;
use Infrastructure\Repository\OrderRepository;
use Infrastructure\Services\OrderService;
use App\Observer\OrderNotifier;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class OrderControllerTest extends TestCase
{
    private OrderController $controller;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $repoMock = $this->createMock(OrderRepository::class);
        $service = new OrderService($repoMock, $this->createMock(OrderNotifier::class));
        $this->controller = new OrderController(
            $service,
            $this->createMock(FastFoodItemInterface::class)
        );
    }

    /**
     * @throws ReflectionException
     */
    public function testIndex()
    {
        $result = $this->controller->index();
        $this->assertIsArray($result);
    }

    public function testShow()
    {
        $testOrder = new Order();
        $testOrder->setId(777);
        $testOrder->setClientName('Vasya');
        $testOrder->setClientPhone('0123456789');
        $testOrder->setIngredients(['onion']);
        $testOrder->setPrice(500);
        $testOrder->setStatus('created');
        $testOrder->setProduct('burger');

        $result = json_encode($testOrder->toArray());

        $this->assertIsString($result);
        $this->assertJson($result);
        $this->assertStringContainsString('"id":777', $result);
    }
}