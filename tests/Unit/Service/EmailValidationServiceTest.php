<?php
namespace Aovchinnikova\Hw15\Tests\Unit\Service;

use Aovchinnikova\Hw15\Service\EmailValidationService;
use PHPUnit\Framework\TestCase;

class EmailValidationServiceTest extends TestCase
{
    private EmailValidationService $service;

    protected function setUp(): void
    {
        $this->service = new EmailValidationService();
    }

    public function testValidateWithValidEmail()
    {
        $result = $this->service->validate('test@example.com');
        $this->assertTrue($result->isValidFormat());
        $this->assertTrue($result->hasValidDNS());
    }

    public function testValidateWithInvalidFormat()
    {
        $result = $this->service->validate('invalid-email');
        $this->assertFalse($result->isValidFormat());
        $this->assertFalse($result->hasValidDNS());
    }

    public function testValidateWithValidFormatButInvalidDNS()
    {
        $result = $this->service->validate('test@nonexistentdomain12345.com');
        $this->assertTrue($result->isValidFormat());
        $this->assertFalse($result->hasValidDNS());
    }

    public function testIsValidFormat()
    {
        $method = new \ReflectionMethod($this->service, 'isValidFormat');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($this->service, 'valid@example.com'));
        $this->assertFalse($method->invoke($this->service, 'invalid'));
    }

    public function testHasValidDNSWithInvalidEmail()
    {
        $method = new \ReflectionMethod($this->service, 'hasValidDNS');
        $method->setAccessible(true);
    
        $this->assertFalse($method->invoke($this->service, 'invalid-email'));
    }
}
