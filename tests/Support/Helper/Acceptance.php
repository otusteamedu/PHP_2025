<?php

namespace Tests\Support\Helper;

use Codeception\Module;

class Acceptance extends Module
{
    // Here you can define custom actions
    // All public methods declared in helper class will be available in $I
    
    /**
     * HOOK: after suite
     *
     * @param array $settings
     */
    public function _afterSuite($settings = [])
    {
        // Clean up after suite execution if needed
    }
}