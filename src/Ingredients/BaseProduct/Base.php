<?php

namespace Shop\Ingredients\BaseProduct;

use Shop\Ingredients\Component;

interface Base extends Component
{
    public function hasSesame(): bool;
}