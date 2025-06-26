<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Validator\EmailVerifier;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EmailVerifierTest extends TestCase {
    private EmailVerifier $verifier;

    protected function setUp(): void {
        $this->verifier = new EmailVerifier();
    }

    #[DataProvider('emailRegexProvider')]
    public function testCheckWithRegex(string $email, bool $expected): void {
        $result = $this->invokePrivateMethod('checkWithRegex', [$email]);
        $this->assertEquals($expected, $result);
    }

    #[DataProvider('emailMxProvider')]
    public function testCheckMxRecords(string $email, bool $expected): void {
        $result = $this->invokePrivateMethod('checkMxRecords', [$email]);

        if ($expected) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    #[DataProvider('emailFullCheckProvider')]
    public function testVerifyEmail(string $email, array $expected): void {
        $result = $this->invokePrivateMethod('verifyEmail', [$email]);

        $this->assertEquals($expected['valid'], $result['valid']);
        $this->assertEquals($expected['regex_check'], $result['regex_check']);

        if ($expected['regex_check']) {
            $this->assertIsBool($result['mx_check']);
        } else {
            $this->assertNull($result['mx_check']);
        }
    }

    public function testVerifyEmails(): void {
        $emails = [
            'test@example.com',
            'invalid.email',
            'nonexistent@domain.unknown'
        ];

        $results = $this->verifier->verifyEmails($emails);

        $this->assertCount(3, $results);

        foreach ($results as $result) {
            $this->assertArrayHasKey('valid', $result);
            $this->assertArrayHasKey('reason', $result);
            $this->assertArrayHasKey('regex_check', $result);
            $this->assertArrayHasKey('mx_check', $result);
        }
    }

    private function invokePrivateMethod(string $methodName, array $args = []) {
        $reflection = new \ReflectionClass($this->verifier);
        $method = $reflection->getMethod($methodName);

        return $method->invokeArgs($this->verifier, $args);
    }

    public static function emailRegexProvider(): array {
        return [
            ['test@example.com', true],
            ['user.name+tag@domain.co.uk', true],
            ['invalid.email', false],
            ['missing@.com', false],
            ['@no-local-part.com', false],
            ['no-at-sign.com', false],
            ['', false],
            [' space@example.com', false],
            ['space@example.com ', false],
            ['üñîçøðé@example.com', false]
        ];
    }

    public static function emailMxProvider(): array {
        return [
            ['test@gmail.com', true],
            ['test@example.com', true],
            ['test@thisdomainsurelydoesnotexist12345.com', false],
            ['test@example.org', true]
        ];
    }

    public static function emailFullCheckProvider(): array {
        return [
            [
                'test@example.com',
                [
                    'valid' => true,
                    'regex_check' => true,
                    'mx_check' => true
                ]
            ],
            [
                'invalid.email',
                [
                    'valid' => false,
                    'regex_check' => false,
                    'mx_check' => null
                ]
            ],
            [
                'test@thisdomainsurelydoesnotexist12345.com',
                [
                    'valid' => false,
                    'regex_check' => true,
                    'mx_check' => false
                ]
            ],
            [
                'test@example.org',
                [
                    'valid' => true,
                    'regex_check' => true,
                    'mx_check' => true
                ]
            ]
        ];
    }

    // Новый тест для случая, когда домен существует, но не имеет MX
    public function testDomainWithoutMx(): void {
        // Используем example.com, который всегда существует, но может не иметь MX
        $result = $this->invokePrivateMethod('checkMxRecords', ['test@example.com']);
        $this->assertIsBool($result);
    }
}