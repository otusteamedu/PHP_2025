<?php

namespace Tests\Unit\Infrastructure\Repository;

use Domain\Models\Order;
use Infrastructure\Repository\OrderRepository;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

class OrderRepositoryTest extends TestCase
{
    private OrderRepository $repository;
    private $pdoMock;
    private $stmtMock;

    /**
     * @throws Exception
     * @throws ReflectionException
     */
    protected function setUp(): void
    {
        $this->pdoMock = $this->createMock(PDO::class);
        $this->stmtMock = $this->createMock(PDOStatement::class);

        $this->repository = new OrderRepository();
        $this->setProtectedProperty($this->repository, $this->pdoMock);
    }

    public function testGetOrderList()
    {
        $this->pdoMock->method('prepare')
            ->willReturn($this->stmtMock);

        $this->stmtMock->method('execute');
        $this->stmtMock->method('fetch')
            ->willReturnOnConsecutiveCalls(
                ['id' => 1, 'client_name' => 'Test'],
                false
            );

        $result = $this->repository->getOrderList();
        $this->assertIsArray($result);
        $this->assertInstanceOf(Order::class, $result[0]);
    }

    /**
     * @throws ReflectionException
     */
    private function setProtectedProperty($object, $value): void
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty('pdo');
        $property->setValue($object, $value);
    }
}
