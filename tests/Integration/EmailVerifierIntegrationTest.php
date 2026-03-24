<?php
namespace App\Tests\Integration;

use PHPUnit\Framework\TestCase;
use App\EmailVerifier;

class EmailVerifierIntegrationTest extends TestCase
{
    private EmailVerifier $verifier;

    protected function setUp(): void
    {
        $this->verifier = new EmailVerifier();
    }

    /**
     * @group integration
     */
    public function testVerifyRealEmailWithMxRecord(): void
    {
        // Используем известные домены с гарантированными MX-записями
        $this->assertTrue($this->verifier->verify('test@gmail.com'), 'Gmail should have MX records');
        $this->assertTrue($this->verifier->verify('admin@yandex.ru'), 'Yandex should have MX records');
    }

    /**
     * @group integration
     */
    public function testVerifyRealEmailWithoutMxRecord(): void
    {
        // Используем домен, который заведомо не имеет MX записей (или вообще не существует)
        $this->assertFalse($this->verifier->verify('test@nonexistent-domain-123456789.com'));
    }
}
