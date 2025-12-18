<?php

declare(strict_types=1);

namespace tests;

use App\Application\CalculateBuilder;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    private CalculateBuilder $calculate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calculate = new CalculateBuilder();
    }

    public function testDefaultOperation(): void
    {
        $calculate = $this->calculate
            ->setOperation()
            ->setNumber1('1')
            ->setNumber2('1')
            ->getInstance();

        $result = $calculate->getResult();

        $this->assertEquals(2, $result);
    }

    public function testOperationMinus()
    {
        $calculate = $this->calculate
            ->setOperation('minus')
            ->setNumber1('1')
            ->setNumber2('1')
            ->getInstance();

        $result = $calculate->getResult();

        $this->assertEquals(0, $result);
    }

    public function testOperationMultiply()
    {
        $calculate = $this->calculate
            ->setOperation('multiply')
            ->setNumber1('2')
            ->setNumber2('3')
            ->getInstance();

        $result = $calculate->getResult();

        $this->assertEquals(6, $result);
    }

    public function testOperationDivide()
    {
        $calculate = $this->calculate
            ->setOperation('divide')
            ->setNumber1('10')
            ->setNumber2('2')
            ->getInstance();

        $result = $calculate->getResult();

        $this->assertEquals(5, $result);
    }

    public function testOperationDivideZero()
    {
        $calculate = $this->calculate
            ->setOperation('divide')
            ->setNumber1('10')
            ->setNumber2('0')
            ->getInstance();

        $result = $calculate->getResult();

        $this->assertEquals(0, $result);
    }

    public function testNumberException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->calculate->setNumber1('text');
    }

    public function testOperationException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->calculate->setOperation('text');
    }

    public function testGetMethods()
    {
        $calculate = $this->calculate
            ->setOperation('minus')
            ->setNumber1('10')
            ->setNumber2('20')
            ->getInstance();

        $this->assertEquals('minus', $calculate->getOperation());
        $this->assertEquals(10, $calculate->getNumber1());
        $this->assertEquals(20, $calculate->getNumber2());
    }
}
