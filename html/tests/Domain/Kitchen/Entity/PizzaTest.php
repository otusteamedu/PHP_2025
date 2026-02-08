<?php

declare(strict_types=1);

namespace Otus\Food\Tests\Domain\Kitchen\Entity;

use Otus\Food\Domain\Kitchen\Entity\Pizza;
use PHPUnit\Framework\TestCase;

class PizzaTest extends TestCase
{
    public function testGetTitle(): void
    {
        $pizza = new Pizza();
        $this->assertSame('Pizza', $pizza->getTitle());
    }

    public function testGetRecept(): void
    {
        $pizza = new Pizza();
        $this->assertSame(['focaccia'], $pizza->getRecept());
    }
}
