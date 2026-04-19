<?php

namespace App\Domain\Product\Chain;

use App\Domain\Exception\MissingIngredientsException;

class CheckIngredientsHandler extends BaseCookingHandler
{

    /**
     * @throws MissingIngredientsException
     */
    protected function process(CookingContext $context): void
    {
       $allIngredients = $this->getAllIngredients($context);
       $notEnoughIngredients = [];
       foreach ($allIngredients as $name => $count) {
           if(!$context->storage->has($name, $count)) {
               $notEnoughIngredients[] = $name;
           }
       }

       if (!empty($notEnoughIngredients)) {
            throw new MissingIngredientsException("There are not enough ingredients for cooking the {$context->product->getType()}: "
                . implode(', ', $notEnoughIngredients));
       }
    }

    private function getAllIngredients(CookingContext $context): array
    {
        $result = $context->baseIngredients;

        foreach ($context->additionalIngredients as $name => $ingredientCount) {
            $result[$name] = isset($result[$name]) ? $result[$name] + $ingredientCount: $ingredientCount;
        }

        return $result;
    }
}