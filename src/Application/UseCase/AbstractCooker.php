<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\Food\FoodDecoratorInterface;
use App\Domain\Entity\Food\FoodInterface;

abstract class AbstractCooker
{
    private FoodDecoratorInterface|FoodInterface $food;

    public function __construct(
        FoodDecoratorInterface $food
    )
    {
        $this->food = $food;
    }

    public function cocking(): void
    {
        $this->beforeCook();
        $this->cook();
        $this->afterCook();
    }

    private function beforeCook(): void
    {
        $this->food->setCookingStatus('started');
    }

    abstract protected function cook(): void;

    private function afterCook(): void
    {
        $this->food->setCookingStatus('finished');

        if (!$this->meetsStandards()) {
            $this->food->setCookingStatus('discarded');
        }
    }

    private function meetsStandards(): bool
    {
        return (bool)\rand(0, 1);
    }

    public function getFood(): string
    {
        if ($this->food->getCookingStatus() === 'discarded') {
            return '';
        }

        return $this->food->getProductComposition();
    }
}
