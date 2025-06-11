<?php

declare(strict_types=1);

namespace App\Application\Factory;

use App\Domain\Entity\Food\{Burger, FoodInterface, Sandwich};
use App\Domain\Factory\FoodFactoryInterface;
use Exception;

final readonly class FoodFactory implements FoodFactoryInterface
{
    public function __construct(
        private Burger   $burger,
        private Sandwich $sandwich
    )
    {
    }

    /**
     * @throws Exception
     */
    public function createFood(string $foodName): FoodInterface
    {
        $text = \mb_strtolower($foodName);

        $productInstance = match ($text) {
            'burger' => $this->burger,
            'sandwich' => $this->sandwich,
            default => null,
        };

        if ($productInstance === null) {
            throw new Exception('Not a valid product name: ' . $text);
        }

        return $productInstance;
    }
}
