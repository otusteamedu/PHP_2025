<?php

declare(strict_types=1);

namespace Ak\Hw\Application\Factory;

class PreCookingEvent implements CookingEventInterface
{
    public function handle(string $product): string
    {
        return "Событие перед приготовлением: $product\n";
    }
}
