<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Model\Product\Decorators\Selector;

use Dinargab\Homework15\Model\Product\Decorators\BaconProductDecorator;
use Dinargab\Homework15\Model\Product\Decorators\CheeseProductDecorator;
use Dinargab\Homework15\Model\Product\Decorators\DefectiveProductDecorator;
use Dinargab\Homework15\Model\Product\Decorators\OnionProductDecorator;
use Dinargab\Homework15\Model\Product\Decorators\PepperProductDecorator;
use Dinargab\Homework15\Model\Product\Decorators\SaladProductDecorator;
use Dinargab\Homework15\Model\Product\Decorators\TomatoProductDecorator;
use Dinargab\Homework15\Model\Product\ProductInterface;
use InvalidArgumentException;

class ProductDecoratorSelector
{
    public function getDecoratedProduct(string $type, ProductInterface $product): ProductInterface
    {
        return match (strtolower($type)) {
            'bacon' => new BaconProductDecorator($product),
            'cheese' => new CheeseProductDecorator($product),
            'onion' => new OnionProductDecorator($product),
            'tomato' => new TomatoProductDecorator($product),
            'salad' => new SaladProductDecorator($product),
            'pepper' => new PepperProductDecorator($product),
            'defective' => new DefectiveProductDecorator($product),
            default => throw new InvalidArgumentException("Unknown ingredient: $type")
        };
    }
}