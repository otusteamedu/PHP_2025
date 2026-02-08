<?php

declare(strict_types=1);

namespace Ak\Hw\Domain\Composite;

interface OrderComponentInterface
{
    public function getPrice(): float;
    public function moveStatus(string $status): string|array;
}
