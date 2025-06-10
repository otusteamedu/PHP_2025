<?php
declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Patterns\Decorator\IngredientDecoratorInterface;

class AddIngredientUseCase
{

    private string $title = '';
    private float $price = 0;

    public function __construct(
        private readonly IngredientDecoratorInterface $ingredientDecorator,
    )
    {
    }

    public function __invoke(): void
    {
        $this->title = $this->ingredientDecorator->getTitle();
        $this->price = $this->ingredientDecorator->getPrice();

        echo $this->title . ' [' . $this->price . ']<br>';
    }

    public function getIngredient(): IngredientDecoratorInterface
    {
        return $this->ingredientDecorator;
    }

    public function getTitle(): string
    {
        return $this->title;
    }


    public function getPrice(): float
    {
        return $this->price;
    }
}