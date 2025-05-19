<?php

use PHPUnit\Framework\TestCase;
use App\Domain\Entity\GetWay\DescGetWay;

class DescGetWayTest extends TestCase
{
    public function testGetGetWayReturnsCorrectString()
    {
        // Создаем экземпляр тестируемого класса
        $descGetWay = new DescGetWay();

        // Вызываем метод getGetWay
        $result = $descGetWay->getGetWay();

        // Проверяем, что результат соответствует ожидаемому значению
        $this->assertEquals("By desc", $result);
    }
}