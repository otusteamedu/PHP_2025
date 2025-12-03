<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Model\Product;


class Burger implements ProductInterface
{
    public string $bun;
    public string $patty;
    public array $toppings = [];

    public function getName(): string
    {
        return 'Burger';
    }

    public function getPrice(): int
    {
        return 20 + count($this->toppings) * 2;
    }

    public function getDescription(): string
    {
        return 'Burger - a sandwich made with a cooked ground meat patty, most commonly beef, served inside a sliced bun.';
    }

    public function getIngredients(): array
    {
        $ingredients = [];
        $ingredients[] = $this->bun;
        $ingredients[] = $this->patty;
        return array_merge($ingredients, $this->toppings);
    }

}