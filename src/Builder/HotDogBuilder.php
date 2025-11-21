<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Builder;

use Dinargab\Homework15\Model\Product\HotDog;
use Dinargab\Homework15\Model\Product\ProductInterface;

class HotDogBuilder implements BuilderInterface
{

    private HotDog $hotdog;

    public function __construct()
    {
        $this->reset();
    }

    public function reset():void
    {
        $this->hotdog = new HotDog();
    }

    public function addFilling(string $filling): self
    {
        $this->hotdog->toppings[] = $filling;
        return $this;
    }

    public function addMainIngredients(): self
    {
        $this->hotdog->bun = "Hot Dog Bun";
        $this->hotdog->sausage = "Hot Dog Sausage";
        return $this;
    }

    public function build(): ProductInterface
    {
        $hotdog = $this->hotdog;
        $this->reset();
        return $hotdog;
    }
}