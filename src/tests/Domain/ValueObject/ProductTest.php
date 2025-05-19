<?php

use App\Domain\ValueObject\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testProductCanBeCreatedWithValidArray(): void
    {
        $value = ['name' => 'Laptop', 'price' => 1000];
        $product = new Product($value);
        $this->assertEquals($value, $product->getValue());
    }

    public function testProductCannotBeCreatedWithEmptyArray(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Заказ не сущесвует!");
        
        new Product([]);
    }

}