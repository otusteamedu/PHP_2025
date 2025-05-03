<?php
declare(strict_types=1);


namespace App\Food\Domain\Aggregate;

use App\Food\Domain\Aggregate\VO\FoodTitle;

interface FoodInterface
{
    public function getId(): string;

    public function getOrderId(): string;

    public function getCookingStatus(): FoodCookingStatusType;

    public function getTitle(): FoodTitle;

    public function getIngredients(): array;

    public function addIngredient(FoodIngredient $ingredient): void;

    public function removeIngredient(FoodIngredient $ingredient): void;

}