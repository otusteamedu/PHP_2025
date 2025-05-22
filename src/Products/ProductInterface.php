<?php
namespace App\Products;

interface ProductInterface
{
    public function getName(): string;
    public function getPrice(): float;
    public function getDescription(): string;
}
