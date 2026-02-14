<?php

declare(strict_types=1);

namespace App\Domain\Cooking\Factory;

use App\Domain\Cooking\Template\AbstractCookingProcess;
use App\Domain\Cooking\Template\BurgerCookingProcess;
use App\Domain\Cooking\Template\SandwichCookingProcess;
use App\Domain\Cooking\Template\HotDogCookingProcess;
use App\Domain\Interfaces\OrderSubjectInterface;
use InvalidArgumentException;

class CookingProcessFactory
{
    public function __construct(
        private OrderSubjectInterface $orderSubject
    ) {}

    public function createProcess(string $productType): AbstractCookingProcess
    {
        return match ($productType) {
            'burger' => new BurgerCookingProcess($this->orderSubject),
            'sandwich' => new SandwichCookingProcess($this->orderSubject),
            'hotdog' => new HotDogCookingProcess($this->orderSubject),
            default => throw new InvalidArgumentException(
                sprintf('Неизвестный тип продукта для готовки: %s', $productType)
            ),
        };
    }
}
