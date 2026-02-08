<?php

declare(strict_types=1);

namespace Ak\Hw\Application\Factory;

interface CookingEventInterface
{
    public function handle(string $product): string;
}
