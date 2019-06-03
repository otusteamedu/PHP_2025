<?php
require_once 'MathClassTest.php';

class MyTestSuite extends \PHPUnit\Framework\TestSuite {
    protected $sharedFixture;
    public static function suite()
    {
        $suite = new MyTestSuite('MyTestSuite');
        $suite->addTestSuite('MathClassTest');
        return $suite;
    }
    protected function setUp():void
    {
        $this->sharedFixture = new MathClassTest();
    }
    protected function tearDown():void
    {
        $this->sharedFixture = NULL;
    }
}
