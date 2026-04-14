<?php

namespace App\Application\DTO;

use App\Domain\Product\ProductInterface;

class PrepareOrderResultDto
{
    /** @var ProductInterface[] */
    private array $products = [];

    /** @var string[] */
    private array $errors = [];

    public function addProduct(ProductInterface $product): void
    {
        $this->products[] = $product;
    }

    public function addError(string $message): void
    {
        $this->errors[] = $message;
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}