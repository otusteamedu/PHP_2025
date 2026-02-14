<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Interfaces\GetMenuUseCaseInterface;
use App\Domain\Product\Factory\ProductFactory;

class GetMenuUseCase implements GetMenuUseCaseInterface
{
    private const AVAILABLE_ADDITIONS = [
        'lettuce' => ['name' => 'Салат', 'price' => 20.00],
        'onion' => ['name' => 'Лук', 'price' => 15.00],
        'pepper' => ['name' => 'Перец', 'price' => 15.00],
        'cheese' => ['name' => 'Сыр', 'price' => 30.00],
        'tomato' => ['name' => 'Помидор', 'price' => 25.00],
    ];

    public function __construct(
        private ProductFactory $productFactory
    ) {}

    public function execute(): array
    {
        $products = [];

        foreach ($this->productFactory->getAvailableTypes() as $type) {
            $product = $this->productFactory->createProduct($type);
            $products[] = [
                'type' => $type,
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'base_price' => $product->getPrice(),
                'base_ingredients' => $product->getIngredients(),
            ];
        }

        return [
            'products' => $products,
            'additions' => self::AVAILABLE_ADDITIONS,
        ];
    }
}
