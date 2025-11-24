<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Model\Product\Decorators;

class DefectiveProductDecorator extends AbstractProductDecorator
{

    public function getIngredients(): array
    {
        return [];
    }
    function getIngredientName(): string
    {
        return "";
    }
}