<?php

namespace App\Builder;

use App\FastFoodItemInterface;

class FastFoodDirector {
    public function __construct(private FastFoodBuilderInterface $builder) {}

    public function build(): FastFoodItemInterface {
        $this->builder->reset();
        $this->builder->setBread();
        $this->builder->setMeat();
        return $this->builder->getProduct();
    }
}