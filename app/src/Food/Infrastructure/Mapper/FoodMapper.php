<?php

declare(strict_types=1);

namespace App\Food\Infrastructure\Mapper;

use App\Food\Domain\Aggregate\Food;
use App\Food\Domain\Aggregate\FoodCookingStatusType;
use App\Food\Domain\Aggregate\FoodIngredient;
use App\Food\Domain\Aggregate\FoodType;
use App\Food\Domain\Aggregate\VO\FoodCalorie;
use App\Food\Domain\Aggregate\VO\FoodMass;
use App\Food\Domain\Aggregate\VO\FoodTitle;
use App\Food\Domain\Factory\BurgerFactory;

readonly class FoodMapper
{
    public function __construct(
        private BurgerFactory $factory,
    ) {
    }

    public function foodMap(array $row): Food
    {
        $food = match ($row['type']) {
            FoodType::BURGER->value => $this->mapBurger($row),
            // todo для остальных типов написать.
        };
        //    private array $foodItems = [];
        $reflection = new \ReflectionClass(get_parent_class($food));
        $property = $reflection->getProperty('id');
        $property->setValue($food, $row['id']);
        $property = $reflection->getProperty('cookingStatus');
        $property->setValue($food, FoodCookingStatusType::from($row['status']));
        $property = $reflection->getProperty('statusCreatedAt');
        $property->setValue($food, new \DateTimeImmutable($row['status_created_at']));
        $property = $reflection->getProperty('statusUpdatedAt');
        $property->setValue($food, new \DateTimeImmutable($row['status_updated_at']));

        return $food;
    }

    private function mapBurger(array $row): Food
    {
        $burger = $this->factory->build($row['order_id'], $row['title']);
        foreach (json_decode($row['ingredients'], true) as $ingredient) {
            $burger->addIngredient(
                new FoodIngredient(
                    new FoodTitle($ingredient['title']),
                    new FoodMass($ingredient['mass']),
                    new FoodCalorie($ingredient['calorie'])
                )
            );
        }

        return $burger;
    }
}
