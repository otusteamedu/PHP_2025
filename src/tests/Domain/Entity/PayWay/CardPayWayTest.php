<?php

use PHPUnit\Framework\TestCase;
use App\Domain\Entity\PayWay\CardPayWay;

class CardPayWayTest extends TestCase
{
    private CardPayWay $payWay;

    protected function setUp(): void
    {
        $this->payWay = new CardPayWay();
    }

    public function testGetPayWayReturnsExpectedString()
    {
        $expectedString = "By card";
        $this->assertEquals($expectedString, $this->payWay->getPayWay(), "Метод getPayWay() должен вернуть строку 'By card'");
    }
}