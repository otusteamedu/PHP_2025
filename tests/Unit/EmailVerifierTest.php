<?php
namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\EmailVerifier;
use App\EmailChecker;

class EmailVerifierTest extends TestCase
{
    /**
     * @dataProvider successfulVerificationProvider
     */
    public function testVerifyReturnsTrueForDifferentValidEmails(string $email, string $domain): void
    {
        $checker = $this->createMock(EmailChecker::class);
        $checker->expects($this->once())
            ->method('checkSyntax')
            ->with($email)
            ->willReturn($domain);

        $checker->expects($this->once())
            ->method('checkDns')
            ->with($domain)
            ->willReturn(true);

        $verifier = new EmailVerifier($checker);
        $this->assertTrue($verifier->verify($email));
    }

    public static function successfulVerificationProvider(): array
    {
        return [
            ['first.last+shop@gmail.com', 'gmail.com'],
            ['team@subdomain.company.org', 'subdomain.company.org'],
            ['user_42@example-mail.net', 'example-mail.net'],
        ];
    }

    public function testVerifyReturnsFalseIfSyntaxIsInvalid(): void
    {
        $checker = $this->createMock(EmailChecker::class);
        $checker->expects($this->once())
            ->method('checkSyntax')
            ->with('invalid-email')
            ->willReturn(false);
            
        $checker->expects($this->never())
            ->method('checkDns');

        $verifier = new EmailVerifier($checker);
        $this->assertFalse($verifier->verify('invalid-email'));
    }

    public function testVerifyReturnsFalseIfDnsFails(): void
    {
        $checker = $this->createMock(EmailChecker::class);
        $checker->expects($this->once())
            ->method('checkSyntax')
            ->with('test@example.com')
            ->willReturn('example.com');
            
        $checker->expects($this->once())
            ->method('checkDns')
            ->with('example.com')
            ->willReturn(false);

        $verifier = new EmailVerifier($checker);
        $this->assertFalse($verifier->verify('test@example.com'));
    }

    public function testVerifyReturnsTrueIfAllChecksPass(): void
    {
        $checker = $this->createMock(EmailChecker::class);
        $checker->expects($this->once())
            ->method('checkSyntax')
            ->with('valid@gmail.com')
            ->willReturn('gmail.com');
            
        $checker->expects($this->once())
            ->method('checkDns')
            ->with('gmail.com')
            ->willReturn(true);

        $verifier = new EmailVerifier($checker);
        $this->assertTrue($verifier->verify('valid@gmail.com'));
    }
}
