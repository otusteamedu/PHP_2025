<?php

declare(strict_types=1);

namespace unit\Application;

use App\Application\VerificationEmailService;
use App\Domain\Email;
use App\Infrastructure\CheckedEmailsFileWriter;
use App\Infrastructure\DnsEmailVerifier;
use App\Infrastructure\EmailsFileReader;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\MockObject;

final class VerificationEmailServiceTest extends Unit
{
    private CheckedEmailsFileWriter|MockObject $checkedEmailsFileWriter;
    private EmailsFileReader|MockObject $fileEmailsReader;
    private DnsEmailVerifier|MockObject $dnsEmailsVerifier;

    private VerificationEmailService $verificationEmailService;

    public function setUp(): void
    {
        $this->checkedEmailsFileWriter = $this->createMock(CheckedEmailsFileWriter::class);
        $this->fileEmailsReader = $this->createMock(EmailsFileReader::class);
        $this->dnsEmailsVerifier = $this->createMock(DnsEmailVerifier::class);

        $this->verificationEmailService = new VerificationEmailService(
            checkedEmailsFileWriter: $this->checkedEmailsFileWriter,
            fileEmailsReader: $this->fileEmailsReader,
            dnsEmailsVerifier: $this->dnsEmailsVerifier,
        );
    }

    public function testHandle(): void
    {
        $this->fileEmailsReader
            ->expects($this->once())
            ->method('readEmails')
            ->willReturn([
                'valid@example.com',
                'test@domain.com',
                'invalid-email'
            ]);

        $this->dnsEmailsVerifier
            ->expects($this->exactly(2))
            ->method('verify')
            ->willReturnCallback(function (Email $email) {
                return str_contains($email->getValue(), 'valid');
            });

        $this->checkedEmailsFileWriter
            ->expects($this->once())
            ->method('writeEmails')
            ->with(['valid@example.com']);

        $this->verificationEmailService->handle();
    }
}
