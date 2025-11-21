<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Builder;

use Dinargab\Homework15\Model\Product\ProductInterface;
use Dinargab\Homework15\Model\Product\Sandwich;

class SandwichBuilder implements BuilderInterface
{
    private Sandwich $sandwich;


    public function __construct()
    {
        $this->reset();
    }

    public function reset():void
    {
        $this->sandwich = new Sandwich();
    }

    public function addMainIngredients(): self
    {
        $this->sandwich->bread = "Bread Slice";
        $this->sandwich->fillings[] = "Ham";
        return $this;
    }

    public function addFilling(string $filling): self
    {
        $this->sandwich->fillings[] = $filling;
        return $this;
    }

    public function build(): ProductInterface
    {
        $sandwich = $this->sandwich;
        $this->reset();
        return $sandwich;
    }
}