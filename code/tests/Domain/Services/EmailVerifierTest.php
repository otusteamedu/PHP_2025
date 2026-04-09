<?php

declare(strict_types=1);

namespace Alisaselezneva\Code\Tests\Domain\Services;

use Alisaselezneva\Code\Domain\Services\Checkers\EmailFormatCheckerInterface;
use Alisaselezneva\Code\Domain\Services\Checkers\MxRecordCheckerInterface;
use Alisaselezneva\Code\Domain\Services\EmailVerifier;
use Alisaselezneva\Code\Domain\Services\VerificationResult;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class EmailVerifierTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var EmailFormatCheckerInterface&MockInterface */
    private $formatChecker;

    /** @var MxRecordCheckerInterface&MockInterface */
    private $mxRecordChecker;

    /** @var EmailVerifier&MockInterface */
    private $verifier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formatChecker = Mockery::mock(EmailFormatCheckerInterface::class);
        $this->mxRecordChecker = Mockery::mock(MxRecordCheckerInterface::class);

        $this->verifier = Mockery::mock(EmailVerifier::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $this->verifier->shouldReceive('getFormatChecker')->andReturn($this->formatChecker);
        $this->verifier->shouldReceive('getMxRecordChecker')->andReturn($this->mxRecordChecker);
    }

    public function testVerifyValid(): void
    {
        $validEmail = 'user@example.com';

        $this->prepareFormatChecker($validEmail, true);
        $this->prepareMxRecordChecker($validEmail, true);

        $result = $this->verifier->verify($validEmail);

        $this->assertInstanceOf(VerificationResult::class, $result);
        $this->assertTrue($result->isValid());
        $this->assertSame(
            [
                'format' => true,
                'mx_records' => true,
            ],
            $result->getChecks()
        );
    }

    public function testVerifyInvalid(): void
    {
        $invalidFormatEmail = 'user@example';

        $this->prepareFormatChecker($invalidFormatEmail, false);
        $this->mxRecordChecker
            ->shouldNotReceive('hasMxRecords');

        $result = $this->verifier->verify($invalidFormatEmail);

        $this->assertInstanceOf(VerificationResult::class, $result);
        $this->assertFalse($result->isValid());
        $this->assertSame(
            [
                'format' => false,
            ],
            $result->getChecks()
        );
    }

    public function testVerifyInvalidMx(): void
    {
        $emailWithoutMx = 'user@no-mx.example';

        $this->prepareFormatChecker($emailWithoutMx, true);
        $this->prepareMxRecordChecker($emailWithoutMx, false);

        $result = $this->verifier->verify($emailWithoutMx);

        $this->assertInstanceOf(VerificationResult::class, $result);
        $this->assertFalse($result->isValid());
        $this->assertSame(
            [
                'format' => true,
                'mx_records' => false,
            ],
            $result->getChecks()
        );
    }

    private function prepareFormatChecker(string $email, bool $result): void
    {
        $this->formatChecker
            ->shouldReceive('isValid')
            ->once()
            ->with($email)
            ->andReturn($result);
    }

    private function prepareMxRecordChecker(string $email, bool $result): void
    {
        $this->mxRecordChecker
            ->shouldReceive('hasMxRecords')
            ->once()
            ->with($email)
            ->andReturn($result);
    }
}
