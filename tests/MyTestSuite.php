<?php
require_once 'MathClassTest.php';
require_once  'ProductClassTest.php';

class MyTestSuite extends \PHPUnit\Framework\TestSuite {
    protected $sharedFixture;
    public static function suite()
    {
        $suite = new MyTestSuite('MyTestSuite');
        $suite->addTestSuite('MathClassTest');
        $suite->addTestSuite('app\tests\ProductClassTest');
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
