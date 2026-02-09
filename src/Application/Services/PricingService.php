<?php

namespace Restaurant\Application\Services;

use Restaurant\Domain\Interfaces\PricingServiceInterface;
use Restaurant\Domain\Enums\IngredientType;
use Restaurant\Domain\Enums\ProductType;

// --- Класс-заглушка для тестов
class PricingService implements PricingServiceInterface
{
    private array $ingredientPrices = [
        IngredientType::SALAD->value => 20.0,
        IngredientType::ONION->value => 10.0,
        IngredientType::PEPPER->value => 15.0,
        IngredientType::MAYO->value => 5.0,
        IngredientType::KETCHUP->value => 5.0,
        IngredientType::MUSTARD->value => 5.0,
        IngredientType::CHEESE->value => 25.0,
        IngredientType::BACON->value => 30.0,
        IngredientType::SAUSAGE->value => 20.0,
        IngredientType::CUCUMBER->value => 10.0,
        IngredientType::SAUSAGE_HOT_DOG->value => 25.0,
        IngredientType::BUN->value => 10.0,
        IngredientType::BEEF_PATTY->value => 40.0,
        IngredientType::BREAD->value => 5.0,
        IngredientType::TOMATO->value => 15.0,
        IngredientType::CARROT->value => 8.0,
        IngredientType::HAM->value => 35.0,
        IngredientType::BAGEL->value => 12.0,
        IngredientType::BUTTER->value => 10.0,
        IngredientType::SPICES->value => 5.0,
    ];

    private array $productBasePrices = [
        ProductType::BURGER->value => 150.0,
        ProductType::SANDWICH->value => 100.0,
        ProductType::HOTDOG->value => 80.0,
    ];

    public function getIngredientPrice(IngredientType $ingredient): float
    {
        return $this->ingredientPrices[$ingredient->value] ?? 0.0;
    }

    public function getProductBasePrice(ProductType $productName): float
    {
        return $this->productBasePrices[$productName->value] ?? 0.0;
    }

    public function setIngredientPrice(IngredientType $ingredient, float $price): void
    {
        $this->ingredientPrices[$ingredient->value] = $price;
    }

    public function setProductBasePrice(ProductType $productName, float $price): void
    {
        $this->productBasePrices[$productName->value] = $price;
    }
}
