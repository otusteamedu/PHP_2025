<?php

namespace Tests\Domain\ValueObject;

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
/* 
class User
{
    private string $value;

    public function __construct(string $value)
    {
        $this->assertValidName($value);
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function assertValidName(string $value): void
    {
        if(!is_numeric($value)){
            throw new \InvalidArgumentException("Пользователь {$value} не сущесвует!");
        }
    }
} */