<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Service\Cooking;

use Dinargab\Homework15\Exception\DefectiveProductException;
use Dinargab\Homework15\Model\Product\Burger;
use Dinargab\Homework15\Model\Product\HotDog;
use Dinargab\Homework15\Model\Product\ProductInterface;
use Dinargab\Homework15\Model\Product\Sandwich;

abstract class AbstractCookProcess implements CookProcessInterface
{
    public function cook(string $type, array $additionalIngredients): ProductInterface
    {
        $this->prepare($type, $additionalIngredients);
        $product = $this->cookProduct($type, $additionalIngredients);
        $product = $this->checkAfter($product);
        return $product;
    }

    protected function prepare(string $type, array $additionalIngredients): void
    {

        $echoString = "Starting process of cooking $type";
        if ($additionalIngredients) {
            foreach ($additionalIngredients as $key => $ingredient) {
                $echoString .= ($key === 0 ? " with" : "") . " $ingredient, ";
            }
        }
        echo $echoString;
    }

    abstract protected function cookProduct(string $type, array $additionalIngredients): ProductInterface;

    protected function checkAfter(ProductInterface $product): ProductInterface
    {
        if ($this->isValid($product)) {
            return $product;
        }
        throw new DefectiveProductException("Alarm! Defective product!!!");
    }

    protected function isValid(ProductInterface $product): bool
    {
        $minIngredients = match (get_class($product)) {
            Burger::class, Sandwich::class => 3,
            HotDog::class => 2,
            default => 2
        };
        return count($product->getIngredients()) >= $minIngredients;
    }

}