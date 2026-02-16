<?php

namespace App\Domain\Entity;

use App\Application\Decorator\Ingredient\BreadDecorator;
use App\Application\Decorator\Ingredient\BunDecorator;
use App\Domain\Entity\Interface\ProductInterface;
use App\Domain\Cooking\CookingStatus;

abstract class Product implements ProductInterface
{
    protected string $name;

    protected array $ingredients = [];

    protected string $cookingStatus;

    public function __construct()
    {
        $this->cookingStatus = CookingStatus::CREATED;
    }

    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCookingStatus(): string
    {
        return $this->cookingStatus;
    }

    public function setCookingStatus(string $status): void
    {
        $this->cookingStatus = $status;
    }

    public function isMeetsStandard(): bool
    {
        $ingredients = $this->getIngredients();

        $isBunIncluded = in_array(BunDecorator::BUN, $ingredients, true);
        $isBreadIncluded = in_array(BreadDecorator::BREAD, $ingredients, true);
        $isEqualsMinQuantity = count($ingredients) >= 2;

        return ($isBunIncluded || $isBreadIncluded) && $isEqualsMinQuantity;
    }
}
