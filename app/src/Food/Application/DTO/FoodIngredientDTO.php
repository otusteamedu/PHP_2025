<?php
declare(strict_types=1);


namespace App\Food\Application\DTO;

class FoodIngredientDTO
{
    public ?string $title;
    public ?int $mass;
    public ?int $calorie;
}