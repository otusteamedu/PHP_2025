<?php

namespace Tests\Unit;

use App\App;
use App\Validator;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    private App $app;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app = new App();
    }

    public function testRunWithValidAndInvalidEmails()
    {
        $emails = [
            "test@example.com",
            "invalid-email",
            "user@unrealdomain.xyz"
        ];

        $result = $this->app->run($emails);

        $this->assertStringContainsString('test@example.com is valid', $result);
        $this->assertStringContainsString('invalid-email is invalid', $result);
        $this->assertStringContainsString('user@unrealdomain.xyz is invalid', $result);
        $this->assertStringContainsString('<br>', $result);
    }

    public function testRunWithEmptyArray()
    {
        $result = $this->app->run([]);
        $this->assertEquals('', $result);
    }

    public function testRunWithSingleValidEmail()
    {
        $emails = ["test@example.com"];
        $result = $this->app->run($emails);
        $this->assertEquals('test@example.com is valid.', $result);
    }

    public function testRunWithSingleInvalidEmail()
    {
        $emails = ["invalid-email"];
        $result = $this->app->run($emails);
        $this->assertEquals('invalid-email is invalid.', $result);
    }
}