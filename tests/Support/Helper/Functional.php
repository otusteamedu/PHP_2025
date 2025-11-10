<?php

namespace Tests\Support\Helper;

use Codeception\Module;

class Functional extends Module
{
    // Here you can define custom actions
    // All public methods declared in helper class will be available in $I
    
    /**
     * HOOK: after test
     *
     * @param \Codeception\TestInterface $test
     */
    public function _after(\Codeception\TestInterface $test)
    {
        // Clean up after each test if needed
    }
}