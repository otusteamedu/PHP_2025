<?php
require "MathClass.php";

class MathClassTest extends \PHPUnit\Framework\TestCase
{
    protected $fixture;

    /**
     * @dataProvider providerFactorial
     */
    public function testFactorial($a,$b)
    {
        //$my = new MathClass();
        $this->assertEquals($b, $this->fixture->factorial($a));
    }
    public function providerFactorial()
    {
        return array (
            array (0, 1),
            array (2, 2),
            array (5, 120)
        );
    }
    protected function setUp()
    {
        $this->fixture = new MathClass();
    }
    protected function tearDown()
    {
        $this->fixture = NULL;
    }

}