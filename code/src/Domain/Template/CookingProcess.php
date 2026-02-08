<?php

declare(strict_types=1);

namespace Ak\Hw\Domain\Template;

abstract class CookingProcess
{
    final public function cook(string $baseProduct, array $customIngredients = []): string
    {
        $product = $this->addIngredients($baseProduct, $customIngredients);
        return $this->package($product);
    }

    abstract protected function addIngredients(string $baseProduct, array $customIngredients): string;

    private function package(string $product): string
    {
        return $product . ' (упаковано)';
    }
}
