<?php

namespace App\Domain\Product\Proxy;

use App\Domain\Exception\MissingIngredientsException;
use App\Domain\Product\Chain\CheckIngredientsHandler;
use App\Domain\Product\Chain\CookingContext;

class CheckIngredientsHandlerProxy extends CheckIngredientsHandler
{
    /**
     * @throws MissingIngredientsException
     */
    protected function process(CookingContext $context): void
    {
        $this->beforeEvent($context);
        try {
            parent::process($context);
            $this->afterEvent($context);
        } catch (MissingIngredientsException $e) {
            $this->afterEvent($context, $e);
            throw $e;
        }
    }
    protected function beforeEvent(CookingContext $context): void
    {
        // do something
    }
    protected function afterEvent(CookingContext $context, ?MissingIngredientsException $e = null): void
    {
        // do something
    }
}