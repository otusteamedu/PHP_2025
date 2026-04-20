<?php

namespace App\Domain\Product\Proxy;

use App\Domain\Product\Chain\AddIngredientsHandler;
use App\Domain\Product\Chain\CookingContext;

class AddIngredientsHandlerProxy extends AddIngredientsHandler
{
    protected function process(CookingContext $context): void
    {
        $this->beforeEvent($context);
        parent::process($context);
        $this->afterEvent($context);

    }
    protected function beforeEvent(CookingContext $context): void
    {
        // do something
    }
    protected function afterEvent(CookingContext $context): void
    {
        // do something
    }
}