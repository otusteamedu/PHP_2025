<?php

namespace EmailsVerifier\Tests\Unit;

use EmailsVerifier\Domain\Email;
use EmailsVerifier\Domain\ValidationError;
use EmailsVerifier\Presentation\Services\VerificationResultProcessor;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use EmailsVerifier\Tests\Providers\ValidationResultsProvider;

class VerificationResultProcessorTest extends TestCase
{
    #[DataProvider('validationCasesProvider')]
    public function testProcessResults(
        array $emails, // Используется только в EmailVerificationControllerTest - общий провайдер
        array $input,
        int $expectedTotal,
        int $expectedValid,
        int $expectedInvalid
    ): void {
        $processed = VerificationResultProcessor::processResults($input);

        $this->assertArrayHasKey('results', $processed);
        $this->assertArrayHasKey('valid_emails', $processed);
        $this->assertArrayHasKey('invalid_emails', $processed);
        $this->assertEquals($expectedTotal, $processed['total_count']);
        $this->assertEquals($expectedValid, $processed['valid_count']);
        $this->assertEquals($expectedInvalid, $processed['invalid_count']);
        $this->assertCount($expectedValid, $processed['valid_emails']);
        $this->assertCount($expectedInvalid, $processed['invalid_emails']);
    }

    public static function validationCasesProvider(): array
    {
        return ValidationResultsProvider::validationCasesProvider();
    }
}
