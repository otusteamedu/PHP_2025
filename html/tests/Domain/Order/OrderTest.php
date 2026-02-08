<?php

declare(strict_types=1);

namespace Otus\Food\Tests\Domain\Order;

use Otus\Food\Domain\Order\Buyer;
use Otus\Food\Domain\Order\Order;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function testOrderAndBuyer(): void
    {
        $name = 'John Doe';
        $buyer = new Buyer($name);
        $order = new Order($buyer);

        $this->assertSame($name, $buyer->name);
        $this->assertSame($name, (string) $buyer);
        $this->assertSame($buyer, $order->getBuyer());
        $this->assertNotEmpty($order->getId());
        $this->assertSame(32, strlen($order->getId())); // bin2hex(16 bytes)
    }
}
