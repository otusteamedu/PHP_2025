<?php

namespace Shop\Ingredients\Filling;

use Shop\Ingredients\Component;

interface Filling extends Component
{
    public function getJuiciness(): int;
}