<?php

declare(strict_types=1);

namespace Ak\Hw\Domain\CookingStrategy;

interface CookingStrategyInterface
{
    public function cook(string $productName): string;
}
