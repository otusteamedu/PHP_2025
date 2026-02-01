<?php

declare(strict_types=1);

namespace Otus\Food\Domain\Kitchen\Entity;

use Otus\Food\Domain\Kitchen\Decorator\Beef;
use Otus\Food\Domain\Kitchen\Decorator\Cheese;
use Otus\Food\Domain\Kitchen\Decorator\Chicken;
use Otus\Food\Domain\Kitchen\Decorator\Cucumber;
use Otus\Food\Domain\Kitchen\Decorator\Ketchup;
use Otus\Food\Domain\Kitchen\Decorator\Lactuca;
use Otus\Food\Domain\Kitchen\Decorator\Mayonnaise;
use Otus\Food\Domain\Kitchen\Decorator\Mushrooms;
use Otus\Food\Domain\Kitchen\Decorator\Olive;
use Otus\Food\Domain\Kitchen\Decorator\Sauce;
use Otus\Food\Domain\Kitchen\Decorator\Tomato;

abstract class Meal
{
    /**
     * @return string
     */
    abstract public function getTitle(): string;

    /**
     * @return array
     */
    abstract public function getRecept(): array;

    /**
     * @return Meal
     */
    public function beef(): self
    {
        return new Beef($this);
    }

    /**
     * @return Meal
     */
    public function cheese(): self
    {
        return new Cheese($this);
    }

    /**
     * @return Meal
     */
    public function chicken(): self
    {
        return new Chicken($this);
    }

    /**
     * @return Meal
     */
    public function cucumber(): self
    {
        return new Cucumber($this);
    }

    /**
     * @return Meal
     */
    public function ketchup(): self
    {
        return new Ketchup($this);
    }

    /**
     * @return Meal
     */
    public function lactuca(): self
    {
        return new Lactuca($this);
    }

    /**
     * @return Meal
     */
    public function mayonnaise(): self
    {
        return new Mayonnaise($this);
    }

    /**
     * @return Meal
     */
    public function mushrooms(): self
    {
        return new Mushrooms($this);
    }

    /**
     * @return Meal
     */
    public function olive(): self
    {
        return new Olive($this);
    }

    /**
     * @return Meal
     */
    public function sauce(): self
    {
        return new Sauce($this);
    }

    /**
     * @return Meal
     */
    public function tomato(): self
    {
        return new Tomato($this);
    }

    /**
     * @return Meal
     */
    public function clone(): self
    {
        return clone $this;
    }
}
