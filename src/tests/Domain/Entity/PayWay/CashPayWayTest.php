<?php

use PHPUnit\Framework\TestCase;
use App\Domain\Entity\PayWay\CashPayWay;

class CashPayWayTest extends TestCase
{
    private CashPayWay $payWay;

    protected function setUp(): void
    {
        $this->payWay = new CashPayWay();
    }

    public function testGetPayWayReturnsExpectedString()
    {
        $expectedString = "By cash";
        $this->assertEquals($expectedString, $this->payWay->getPayWay(), "Метод getPayWay() должен вернуть строку 'By cash'");
    }
}