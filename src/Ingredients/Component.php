<?php

namespace Shop\Ingredients;

interface Component
{
    public function getName(): string;

    public function getPrice(): float;
}