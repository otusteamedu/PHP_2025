<?php

declare(strict_types=1);

namespace Otus\Food\Tests\Presentation\Console;

use Otus\Food\Application\Strategy\Harvester;
use Otus\Food\Application\Strategy\MealStrategy;
use Otus\Food\Domain\Kitchen\Entity\Meal;
use Otus\Food\Presentation\Console\Kitchen;
use PHPUnit\Framework\TestCase;

class KitchenConsoleTest extends TestCase
{
    public function testInvoke(): void
    {
        $meal = $this->createStub(Meal::class);
        $meal->method('getRecept')->willReturn(['item']);
        $meal->method('clone')->willReturn($meal);

        $strategy = $this->createStub(MealStrategy::class);
        $strategy->method('cooking')->willReturn($meal);

        $harvester = new Harvester(['title' => $strategy]);

        $console = new Kitchen($harvester);

        ob_start();
        $result = $console('title', 'me', 2);
        $output = ob_get_clean();

        $this->assertSame(0, $result);
        $this->assertStringContainsString('item', $output);
    }
}
