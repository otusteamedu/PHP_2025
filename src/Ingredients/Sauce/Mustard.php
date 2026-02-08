<?php

namespace Shop\Ingredients\Sauce;

class Mustard implements BaseSauce
{

    public function getName(): string
    {
        return 'Горчица';
    }

    public function getPrice(): float
    {
        return 50;
    }
}