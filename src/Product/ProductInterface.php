<?php

declare(strict_types=1);

namespace Restaurant\Product;

interface ProductInterface
{
    public function getDescription(): string;

    public function getPrice(): float;

    public function cook(): void;

    public function isQualityAcceptable(): bool;
}
