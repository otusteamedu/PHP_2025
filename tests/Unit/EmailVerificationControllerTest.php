<?php

namespace EmailsVerifier\Tests\Unit;

use EmailsVerifier\Application\Interfaces\VerifyEmailsUseCaseInterface;
use EmailsVerifier\Presentation\Controllers\EmailVerificationController;
use EmailsVerifier\Tests\Providers\ValidationResultsProvider;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;

class EmailVerificationControllerTest extends TestCase
{
    private VerifyEmailsUseCaseInterface&MockObject $useCaseMock;
    private EmailVerificationController $controller;

    protected function setUp(): void
    {
        $this->useCaseMock = $this->createMock(VerifyEmailsUseCaseInterface::class);
        $this->controller = new EmailVerificationController($this->useCaseMock);
    }

    #[DataProvider('validationCasesProvider')]
    public function testVerifyEmails(
        array $emails,
        array $results,
        int $totalCount,
        int $validCount,
        int $invalidCount
    ): void {
        $this->useCaseMock->method('execute')->willReturn($results);

        $result = $this->controller->verifyEmails($emails);

        $this->assertArrayHasKey('results', $result);
        $this->assertArrayHasKey('valid_emails', $result);
        $this->assertArrayHasKey('invalid_emails', $result);
        $this->assertEquals($totalCount, $result['total_count']);
        $this->assertEquals($validCount, $result['valid_count']);
        $this->assertEquals($invalidCount, $result['invalid_count']);
        $this->assertCount($validCount, $result['valid_emails']);
        $this->assertCount($invalidCount, $result['invalid_emails']);
    }

    public static function validationCasesProvider(): array
    {
        return ValidationResultsProvider::validationCasesProvider();
    }
}
