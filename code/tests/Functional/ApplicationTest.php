<?php

namespace Tests\Functional;

use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    public function testApplicationOutput()
    {
        ob_start();
        require __DIR__ . '/../../index.php';
        $output = ob_get_clean();

        $this->assertStringContainsString('test@example.com is ', $output);
        $this->assertStringContainsString('invalid-email is invalid', $output);
        $this->assertStringContainsString('user@unrealdomain.xyz is ', $output);
    }
}