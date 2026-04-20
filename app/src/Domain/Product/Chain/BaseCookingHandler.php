<?php

namespace App\Domain\Product\Chain;

abstract class BaseCookingHandler implements CookingHandlerInterface
{
    protected ?CookingHandlerInterface $next = null;

    public function setNext(CookingHandlerInterface $handler): CookingHandlerInterface
    {
        $this->next = $handler;
        return $handler;
    }

    public function handle(CookingContext $context): void
    {
        $this->process($context);

        $this->goToNextHandle($context);
    }

    protected function goToNextHandle(CookingContext $context): void
    {
        if (!is_null($this->next)) {
            $this->next->handle($context);
        }
    }

    abstract protected function process(CookingContext $context): void;
}