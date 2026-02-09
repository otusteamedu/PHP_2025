<?php

namespace Restaurant\Domain\Interfaces;

interface ProductDecoratorInterface extends ProductInterface
{
    public function setProduct(ProductInterface $product): void;
}
