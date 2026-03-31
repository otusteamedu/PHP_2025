<?php

namespace Tests\Integration;

use App\App;
use PHPUnit\Framework\TestCase;

class EmailValidationIntegrationTest extends TestCase
{
    public function testFullEmailValidationFlow()
    {
        $emails = [
            "test@example.com",
            "invalid-email",
            "user@unrealdomain.xyz"
        ];

        $app = new App();
        $result = $app->run($emails);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
        
        // Проверяем, что результат содержит ожидаемые строки
        $lines = explode("<br>", $result);
        $this->assertCount(3, $lines);
        
        foreach ($lines as $line) {
            $this->assertStringContainsString('is ', $line);
        }
    }

    public function testIntegrationWithRealDomains()
    {
        $emails = [
            "test@gmail.com",
            "user@example.com",
            "invalid@format",
        ];

        $app = new App();
        $result = $app->run($emails);

        $this->assertStringContainsString('test@gmail.com is valid', $result);
        $this->assertStringContainsString('user@example.com is valid', $result);
        $this->assertStringContainsString('invalid@format is invalid', $result);
    }
}