<?php
namespace Shop\Ingredients\Meat;

use Shop\Ingredients\Component;

interface BaseMeat extends Component
{
    /**
     * Calories, Fat, Fiber, Protein
     *
     * @return mixed
     */
    public function getNutritionalInfo(): array;

    public function getType(): string;
}