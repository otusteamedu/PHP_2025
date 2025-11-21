<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Builder;

use Dinargab\Homework15\Model\Product\ProductInterface;

interface BuilderInterface
{

    public function addMainIngredients(): self;

    public function addFilling(string $filling): self;

    public function build(): ProductInterface;

    public function reset(): void;

}