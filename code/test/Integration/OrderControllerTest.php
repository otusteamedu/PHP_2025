<?php
declare(strict_types=1);

namespace Ak\Hw\Test\Integration;

use Ak\Hw\Application\Validator;
use Ak\Hw\Domain\OrderRepository;
use Ak\Hw\Infrastructure\ApiService;
use Ak\Hw\Presentation\OrderController;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class OrderControllerTest extends MockeryTestCase
{
    private Validator $validator;
    private Mockery\MockInterface|ApiService $apiServiceMock;
    private Mockery\MockInterface|OrderRepository $orderRepositoryMock;
    private OrderController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new Validator();
        $this->apiServiceMock = Mockery::mock(ApiService::class);
        $this->orderRepositoryMock = Mockery::mock(OrderRepository::class);

        $this->controller = new OrderController(
            $this->validator,
            $this->apiServiceMock,
            $this->orderRepositoryMock
        );
    }

    public function testProcessOrderWithInvalidDataRendersFormWithErrors(): void
    {
        $invalidData = ["card_number" => "invalid", "sum" => -10];
        $this->apiServiceMock->shouldNotReceive('charge');
        $this->orderRepositoryMock->shouldNotReceive('setOrderIsPaid');

        ob_start();
        try {
            $this->controller->processOrder($invalidData);
            $output = ob_get_contents();
        } finally {
            ob_end_clean();
        }

        $this->assertStringContainsString('class="form-control is-invalid"', $output);
        $this->assertStringContainsString('Invalid card number format', $output);
        $this->assertStringContainsString('Sum must be a positive number', $output);
    }

    public function testProcessOrderWithValidDataCallsRepository(): void
    {
        $validData = [
            "card_number" => "4111 1111 1111 1111",
            "card_holder" => "John Doe",
            "card_expiration" => "12/28",
            "cvv" => "602",
            "sum" => '10.1',
            "order_number" => "ORDER-123"
        ];

        $this->apiServiceMock->shouldReceive('charge')->once()->with($validData)->andReturn(true);
        $this->orderRepositoryMock->shouldReceive('setOrderIsPaid')->once()->with('ORDER-123', 10.1)->andReturn(true);

        ob_start();
        try {
            $this->controller->processOrder($validData);
            $output = ob_get_contents();
        } finally {
            ob_end_clean();
        }

        $this->assertStringContainsString('Payment was successful!', $output);
    }

    public function testProcessOrderHandlesRepositoryException(): void
    {
        $validData = [
            "card_number" => "4111 1111 1111 1111",
            "card_holder" => "John Doe",
            "card_expiration" => "12/28",
            "cvv" => "602",
            "sum" => '10.1',
            "order_number" => "UNKNOWN-ORDER"
        ];

        $this->apiServiceMock->shouldReceive('charge')->once()->andReturn(true);
        $this->orderRepositoryMock->shouldReceive('setOrderIsPaid')->once()->andThrow(new \Exception('Order not found'));

        ob_start();
        try {
            $this->controller->processOrder($validData);
            $output = ob_get_contents();
        } finally {
            ob_end_clean();
        }

        $this->assertStringContainsString('Order not found', $output);
    }
}
