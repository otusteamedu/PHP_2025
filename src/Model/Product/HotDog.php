<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Model\Product;


class HotDog implements ProductInterface
{

    public string $bun;
    public string $sausage;

    public array $toppings = [];

    public function getName(): string
    {
        return 'Hot-dog';
    }

    public function getPrice(): int
    {
        return 10;
    }

    public function getDescription(): string
    {
        return 'Hot-dog - a grilled, steamed, or boiled sausage served in the slit of a partially sliced bun';
    }


    public function getIngredients(): array
    {
        $ingredients = [];
        $ingredients[] = $this->bun;
        $ingredients[] = $this->sausage;
        return array_merge($ingredients, $this->toppings);
    }
}