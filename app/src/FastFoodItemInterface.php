<?php

namespace App;

interface FastFoodItemInterface {
    public function getDescription(): string;
    public function getCost(): float;
}
