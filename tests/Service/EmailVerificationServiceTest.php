<?php

namespace Tests\Service;

use App\Service\EmailVerificationService;
use App\Validator\EmailValidator;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use phpmock\mockery\PHPMockery;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class EmailVerificationServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private EmailVerificationService $service;

    protected function setUp(): void
    {
        $this->service = new EmailVerificationService(new EmailValidator());
    }

    #[TestWith(['test@gmail.com'])]
    public function testVerifyValidEmail(string $email): void
    {
        PHPMockery::mock('App\Validator', 'checkdnsrr')
            ->once()
            ->with('gmail.com')
            ->andReturn(true);

        $result = $this->service->verify($email);

        $this->assertTrue($result['format_valid']);
        $this->assertTrue($result['mx_record']);
        $this->assertTrue($result['valid']);
        $this->assertEquals('Valid', $result['reason']);
    }

    #[TestWith(['invalid-email'])]
    public function testVerifyInvalidFormat(string $email): void
    {
        $result = $this->service->verify($email);

        $this->assertFalse($result['format_valid']);
        $this->assertFalse($result['valid']);
        $this->assertEquals('Invalid format', $result['reason']);
    }

    #[TestWith(['email@nksghgjh5.jp'])]
    public function testVerifyNoMxRecord(string $email): void
    {
        PHPMockery::mock('App\Validator', 'checkdnsrr')
            ->once()
            ->with('nksghgjh5.jp')
            ->andReturn(false);

        $result = $this->service->verify($email);

        $this->assertFalse($result['mx_record']);
        $this->assertFalse($result['valid']);
        $this->assertEquals('No MX record', $result['reason']);
    }

    #[TestWith(['test@gmail.com'])]
    public function testVerifyMultipleSkipsEmpty(string $email): void
    {
        PHPMockery::mock('App\Validator', 'checkdnsrr')
            ->once()
            ->with('gmail.com')
            ->andReturn(true);

        $result = $this->service->verifyMultiple(['', $email]);

        $this->assertCount(1, $result);
    }
}
