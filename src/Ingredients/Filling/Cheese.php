<?php

namespace Shop\Ingredients\Filling;

class Cheese implements Filling
{
    public function getName(): string
    {
        return 'Сыр';
    }

    public function getJuiciness(): int
    {
        return 0;
    }

    public function getPrice(): float
    {
        return 100;
    }
}