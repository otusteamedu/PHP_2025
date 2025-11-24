<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Builder\Factory;

use Dinargab\Homework15\Builder\BuilderInterface;
use Dinargab\Homework15\Builder\BurgerBuilder;
use Dinargab\Homework15\Builder\HotDogBuilder;
use Dinargab\Homework15\Builder\SandwichBuilder;
use InvalidArgumentException;

class BuilderFactory
{
    public function __construct(
        private BurgerBuilder   $burgerBuilder,
        private SandwichBuilder $sandwichBuilder,
        private HotDogBuilder   $hotDogBuilder,
    )
    {

    }

    public function getBuilder(string $type): BuilderInterface
    {
        return match ($type) {
            "burger" => $this->burgerBuilder,
            "sandwich" => $this->sandwichBuilder,
            "hotdog" => $this->hotDogBuilder,
            default => throw new InvalidArgumentException("Unknown builder type '$type'"),
        };
    }
}