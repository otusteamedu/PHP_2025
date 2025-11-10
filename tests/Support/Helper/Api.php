<?php

namespace Tests\Support\Helper;

use Codeception\Module;

class Api extends Module
{
    // Here you can define custom actions
    // All public methods declared in helper class will be available in $I
    
    /**
     * HOOK: before suite
     *
     * @param array $settings
     */
    public function _beforeSuite($settings = [])
    {
        // Set up before suite execution if needed
    }
}