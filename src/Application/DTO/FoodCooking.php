<?php

declare(strict_types=1);

namespace App\Application\DTO;

use App\Application\UseCase\FoodDecorator;
use App\Domain\Observer\EventInterface;
use App\Domain\Observer\PublisherInterface;

readonly class FoodCooking implements EventInterface
{
    private string $cookingStatus;

    function __construct(
        private PublisherInterface $publisher
    )
    {
        $this->cookingStatus = 'cooking';
    }

    public function __invoke(FoodDecorator $food): void
    {
        $food->setCookingStatus($this->cookingStatus);
        $this->publisher->notify($food);
    }
}
