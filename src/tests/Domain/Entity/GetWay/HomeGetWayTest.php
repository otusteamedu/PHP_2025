<?php

use PHPUnit\Framework\TestCase;
use App\Domain\Entity\GetWay\HomeGetWay;

class HomeGetWayTest extends TestCase
{
    public function testGetGetWayReturnsCorrectString()
    {
        // Создаем экземпляр тестируемого класса
        $descGetWay = new HomeGetWay();

        // Вызываем метод getGetWay
        $result = $descGetWay->getGetWay();

        // Проверяем, что результат соответствует ожидаемому значению
        $this->assertEquals("To home", $result);
    }
}