<?php

namespace Shop\Ingredients\Filling;

class Tomato implements Filling
{
    public function getName(): string
    {
        return 'Помидор';
    }

    public function getJuiciness(): int
    {
        return 5;
    }

    public function getPrice(): float
    {
        return 1000;
    }
}