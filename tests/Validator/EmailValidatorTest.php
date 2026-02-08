<?php

namespace Tests\Validator;

use App\Validator\EmailValidator;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use phpmock\mockery\PHPMockery;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class EmailValidatorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private EmailValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new EmailValidator();
    }

    #[TestWith(['firstname.lastname@example.com'])]
    #[TestWith(['email@subdomain.example.com'])]
    #[TestWith(['email@example.name'])]
    #[TestWith(['1234567890@example.com'])]
    #[TestWith(['email@example.co.jp'])]
    public function testIsValidFormatReturnsTrue(string $email): void
    {
        $result = $this->validator->isValidFormat($email);

        $this->assertTrue($result);
    }

    #[TestWith(['not-an-email'])]
    public function testIsValidFormatReturnsFalseOnFilterVar(string $email): void
    {
        $result = $this->validator->isValidFormat($email);

        $this->assertFalse($result);
    }

    #[TestWith(['"john"@example.com'])]
    public function testIsValidFormatReturnsFalseOnRegExp(string $email): void
    {
        $result = $this->validator->isValidFormat($email);

        $this->assertFalse($result);
    }

    #[TestWith(['user@example.com'])]
    public function testHasMxRecordReturnsTrue(string $email): void
    {
        PHPMockery::mock('App\Validator', 'checkdnsrr')
            ->once()
            ->with('example.com')
            ->andReturn(true);

        $result = $this->validator->hasMxRecord($email);

        $this->assertTrue($result);
    }

    #[TestWith(['user@nomx.com'])]
    public function testHasMxRecordReturnsFalse(string $email): void
    {
        PHPMockery::mock('App\Validator', 'checkdnsrr')
            ->once()
            ->with('nomx.com')
            ->andReturn(false);

        $result = $this->validator->hasMxRecord($email);

        $this->assertFalse($result);
    }
}
