<?php

namespace Shop\Ingredients\Filling;

class Cucumber implements Filling
{

    public function getName(): string
    {
        return 'Огурец';
    }

    public function getPrice(): float
    {
        return 20;
    }

    public function getJuiciness(): int
    {
        return 2;
    }
}