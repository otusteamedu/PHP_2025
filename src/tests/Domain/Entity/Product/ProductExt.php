<?php

use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Product\BurgerProduct;
use App\Domain\Entity\Product\ProductExt;


class ProductExtTest extends TestCase
{
    // Создаем фиктивный класс для замены реального продукта
    private $mockProduct;

    protected function setUp(): void
    {
        // Создаем мок-объект для продукта
        $this->mockProduct = $this->createMock(BurgerProduct::class);
    }

    public function testMakeProductDelegatesToProduct()
    {
        $title = "Тестовый продукт";
        $expectedOutput = "Тестовый продукт готов."; // Ожидаемое значение

        // Настройка поведения мок-объекта
        $this->mockProduct
            ->expects($this->once()) // Убедимся, что метод вызывается один раз
            ->method('makeProduct')
            ->with($this->equalTo($title)) // Ожидаем, что передан правильный заголовок
            ->willReturn($expectedOutput); // Указываем, что метод будет возвращать это значение

        // Создаем экземпляр ProductExt с мок-объектом
        $productExt = new ProductExt($this->mockProduct);

        // Проверяем, что метод makeProduct возвращает ожидаемое значение
        $this->assertEquals($expectedOutput, $productExt->makeProduct($title), "Метод makeProduct() должен делегировать вызов к продукту и вернуть правильный результат.");
    }
}