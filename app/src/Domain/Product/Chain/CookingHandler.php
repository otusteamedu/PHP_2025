<?php

namespace App\Domain\Product\Chain;

class CookingHandler extends BaseCookingHandler
{
    protected function process(CookingContext $context): void
    {
    }

    public function handle(CookingContext $context): void
    {
        parent::handle($context);
    }
}