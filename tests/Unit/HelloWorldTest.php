<?php


namespace Tests\Unit;

use Tests\Support\UnitTester;

class HelloWorldTest extends \Codeception\Test\Unit
{

    protected UnitTester $tester;

    protected function _before()
    {
    }

    // tests
    public function testSomeFeature()
    {
        $this->assertTrue(true);
    }
}
