<?php

declare(strict_types=1);

namespace Otus\Code\Domain\Product\Contract;

interface ProductInterface
{
    public function getName(): string;

    /**
     * @return string[]
     */
    public function getIngredients(): array;

    public function getBasePrice(): int;

    public function getPrice(): int;

    public function describe(): string;
}
