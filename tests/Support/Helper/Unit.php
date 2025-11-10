<?php

namespace Tests\Support\Helper;

use Codeception\Module;

class Unit extends Module
{
    // Here you can define custom actions
    // All public methods declared in helper class will be available in $I
    
    /**
     * HOOK: before test
     *
     * @param \Codeception\TestInterface $test
     */
    public function _before(\Codeception\TestInterface $test)
    {
        // Set up before each test if needed
    }
}