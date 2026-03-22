<?php

declare(strict_types=1);

namespace Otus\Code\Application\Product\Recipe;

final class Recipe
{
    /**
     * @param class-string[] $decorators
     */
    public function __construct(
        private readonly string $name,
        private readonly array $decorators,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return class-string[]
     */
    public function getDecorators(): array
    {
        return $this->decorators;
    }
}
