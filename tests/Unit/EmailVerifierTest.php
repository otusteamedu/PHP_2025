<?php declare(strict_types=1);

namespace Tests\Unit;

use App\Validator\EmailVerifier;
use PHPUnit\Framework\TestCase;

class EmailVerifierTest extends TestCase
{
    private EmailVerifier $emailVerifier;

    protected function setUp(): void
    {
        $this->emailVerifier = new EmailVerifier();
    }

    public function testValidEmail()
    {
        $result = $this->emailVerifier->verifyEmails(['test@gmail.com']);
        $this->assertArrayHasKey('test@gmail.com', $result);
        $this->assertTrue($result['test@gmail.com']['valid']);
        $this->assertTrue($result['test@gmail.com']['regex_check']);
        $this->assertTrue($result['test@gmail.com']['mx_check']);
    }

    public function testInvalidFormatEmail()
    {
        $result = $this->emailVerifier->verifyEmails(['invalid.email']);
        $this->assertArrayHasKey('invalid.email', $result);
        $this->assertFalse($result['invalid.email']['valid']);
        $this->assertFalse($result['invalid.email']['regex_check']);
        $this->assertNull($result['invalid.email']['mx_check']);
        $this->assertEquals('Invalid email format', $result['invalid.email']['reason']);
    }

    public function testValidFormatButInvalidDomain()
    {
        $result = $this->emailVerifier->verifyEmails(['test@non-existent-domain.xyz']);
        $this->assertArrayHasKey('test@non-existent-domain.xyz', $result);
        $this->assertFalse($result['test@non-existent-domain.xyz']['valid']);
        $this->assertTrue($result['test@non-existent-domain.xyz']['regex_check']);
        $this->assertFalse($result['test@non-existent-domain.xyz']['mx_check']);
        $this->assertFalse($result['test@non-existent-domain.xyz']['dns_check']);
        $this->assertEquals('No MX records found', $result['test@non-existent-domain.xyz']['reason']);
    }

    public function testMultipleEmails()
    {
        $emails = [
            'test@gmail.com',
            'invalid.email',
            'user@yahoo.com'
        ];
        
        $result = $this->emailVerifier->verifyEmails($emails);
        
        $this->assertCount(3, $result);
        $this->assertArrayHasKey('test@gmail.com', $result);
        $this->assertArrayHasKey('invalid.email', $result);
        $this->assertArrayHasKey('user@yahoo.com', $result);
        
        $this->assertTrue($result['test@gmail.com']['valid']);
        $this->assertFalse($result['invalid.email']['valid']);
        $this->assertTrue($result['user@yahoo.com']['valid']);
    }
}