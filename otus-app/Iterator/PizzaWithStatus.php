<?php

declare(strict_types=1);

namespace App\Iterator;

use App\Adapter\Pizza;

class PizzaWithStatus extends Pizza
{
    public PizzaStatusIterator $statusIterator;

    public function __construct(
        ?string $name = null,
        array $ingredientList = [],
        string $description = '',
    ) {
        parent::__construct($name, $ingredientList, $description);
        $this->statusIterator = new PizzaStatusIterator();
    }

    public function getCurrentPizzaStatus(): string
    {
        return $this->statusIterator->current();
    }

    public function movePizzaToNextStatus(): void
    {
        if ($this->statusIterator->hasNext() === true) {
            $this->statusIterator->next();
        }
    }
}
