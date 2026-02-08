<?php

declare(strict_types=1);

namespace Otus\Food\Tests\Application\Builder;

use Otus\Food\Application\Builder\Burger as BurgerBuilder;
use Otus\Food\Domain\Kitchen\Entity\Burger as BurgerEntity;
use Otus\Food\Domain\Kitchen\Entity\Meal;
use Otus\Food\Domain\Order\Buyer;
use Otus\Food\Domain\Order\Order;
use PHPUnit\Framework\TestCase;

class BurgerBuilderTest extends TestCase
{
    public function testCooking(): void
    {
        $meal = new BurgerEntity();
        $order = new Order(new Buyer('Alice'));

        $builder = new BurgerBuilder($meal, $order);
        $cookedMeal = $builder->cooking();

        $this->assertInstanceOf(Meal::class, $cookedMeal);
        $recept = $cookedMeal->getRecept();

        $this->assertContains('beef', $recept);
        $this->assertContains('ketchup', $recept);
    }
}
