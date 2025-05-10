<?php

declare(strict_types=1);

namespace App\Food\Domain\Service\FoodFiller;

use App\Food\Domain\Aggregate\FoodCookingStatusType;
use App\Food\Domain\Aggregate\FoodIngredient;
use App\Food\Domain\Aggregate\FoodInterface;
use App\Food\Domain\Aggregate\FoodType;
use App\Food\Domain\Aggregate\VO\FoodTitle;

readonly class BaseFoodFiller implements FoodInterface
{
    public function __construct(private FoodInterface $food)
    {
    }

    public function addIngredient(FoodIngredient $ingredient): void
    {
        $this->food->addIngredient($ingredient);
    }

    public function getId(): string
    {
        return $this->food->getId();
    }

    public function getOrderId(): string
    {
        return $this->food->getOrderId();
    }

    public function getCookingStatus(): FoodCookingStatusType
    {
        return $this->food->getCookingStatus();
    }

    public function getTitle(): FoodTitle
    {
        return $this->food->getTitle();
    }

    public function getIngredients(): array
    {
        return $this->food->getIngredients();
    }

    public function removeIngredient(FoodIngredient $ingredient): void
    {
        $this->food->removeIngredient($ingredient);
    }

    public function getType(): FoodType
    {
        return $this->food->getType();
    }

    public function setCookingStatus(FoodCookingStatusType $cookingStatus): void
    {
        $this->food->setCookingStatus($cookingStatus);
    }

    public function getStatusCreatedAt(): \DateTimeImmutable
    {
        return $this->food->getStatusCreatedAt();
    }

    public function getStatusUpdatedAt(): \DateTimeImmutable
    {
        return $this->food->getStatusUpdatedAt();
    }
}
