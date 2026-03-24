<?php
namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\EmailChecker;

class EmailCheckerTest extends TestCase
{
    private EmailChecker $checker;

    protected function setUp(): void
    {
        $this->checker = new EmailChecker();
    }

    /**
     * @dataProvider validEmailProvider
     */
    public function testCheckSyntaxValid(string $email, string $expectedDomain): void
    {
        $this->assertEquals($expectedDomain, $this->checker->checkSyntax($email));
    }

    public static function validEmailProvider(): array
    {
        return [
            ['test@example.com', 'example.com'],
            ['user.name+tag@gmail.com', 'gmail.com'],
            ['admin@sub.domain.org', 'sub.domain.org'],
            ['123@456.com', '456.com'],
        ];
    }

    /**
     * @dataProvider invalidEmailProvider
     */
    public function testCheckSyntaxInvalid(string $email): void
    {
        $this->assertFalse($this->checker->checkSyntax($email));
    }

    public static function invalidEmailProvider(): array
    {
        return [
            ['invalid-email'],
            ['@no-user.com'],
            ['user@'],
            ['user@domain'],
            ['user#domain.com'],
            ['user@domain..com'],
        ];
    }
}
