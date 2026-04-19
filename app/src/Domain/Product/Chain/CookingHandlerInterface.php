<?php

namespace App\Domain\Product\Chain;

interface CookingHandlerInterface
{
    public function setNext(CookingHandlerInterface $handler): CookingHandlerInterface;

    public function handle(CookingContext $context): void;
}