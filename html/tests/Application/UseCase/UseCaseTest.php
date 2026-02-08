<?php

declare(strict_types=1);

namespace Otus\Food\Tests\Application\UseCase;

use Otus\Food\Application\Process\ProcessInterface;
use Otus\Food\Application\UseCase\CookingBurger;
use Otus\Food\Application\UseCase\CookingSandwich;
use Otus\Food\Domain\Kitchen\Entity\Meal;
use PHPUnit\Framework\TestCase;

class UseCaseTest extends TestCase
{
    public function testCookingBurger(): void
    {
        $meal = $this->createStub(Meal::class);
        $process = $this->createMock(ProcessInterface::class);
        $process->expects($this->once())->method('run')->willReturn($meal);

        $useCase = new CookingBurger($process);
        $this->assertSame($meal, $useCase->cooking());
    }

    public function testCookingSandwich(): void
    {
        $meal = $this->createStub(Meal::class);
        $process = $this->createMock(ProcessInterface::class);
        $process->expects($this->once())->method('run')->willReturn($meal);

        $useCase = new CookingSandwich($process);
        $this->assertSame($meal, $useCase->cooking());
    }
}
