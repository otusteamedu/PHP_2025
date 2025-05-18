<?php

use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Product\HotdogProduct;
use App\Application\UseCase\MakeProduct\MakeProductUseCase;

class MakeProductUseCaseTest extends TestCase
{
    public function testInvokeCallsMakeProduct()
    {
        // Создаем мока для объекта продукта
        $mockProduct = $this->createMock(HotdogProduct::class);

        // Устанавливаем ожидание, что метод makeProduct будет вызван один раз с параметром "Продукт"
        $mockProduct->expects($this->once())
            ->method('makeProduct')
            ->with($this->equalTo("Продукт"))
            ->willReturn(true);

        // Создаем экземпляр MakeProductUseCase с моком
        $useCase = new MakeProductUseCase($mockProduct);

        // Вызываем метод __invoke
        $result = $useCase();

        // Проверяем, что результат соответствует ожидаемому значению
        $this->assertTrue($result); // Если метод makeProduct возвращает true
    }
}