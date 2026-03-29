<?php

namespace App\Builder;

use App\FastFoodItemInterface;

interface FastFoodBuilderInterface {
    public function reset(): void;
    public function setBread(): void;
    public function setMeat(): void;
    public function getProduct(): FastFoodItemInterface;
}
