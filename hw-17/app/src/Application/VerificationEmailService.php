<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Email;
use App\Infrastructure\CheckedEmailsFileWriter;
use App\Infrastructure\DnsEmailVerifier;
use App\Infrastructure\EmailsFileReader;

readonly class VerificationEmailService
{
    public function __construct(
        private CheckedEmailsFileWriter $checkedEmailsFileWriter,
        private EmailsFileReader $fileEmailsReader,
        private DnsEmailVerifier $dnsEmailsVerifier,
    ) {
    }

    public function handle(): void
    {
        $emails = $this->fileEmailsReader->readEmails();
        $verifiedEmails = $this->verifyEmails($emails);

        $this->checkedEmailsFileWriter->writeEmails($verifiedEmails);
    }

    private function verifyEmails(array $emails): array
    {
        $verified = [];

        foreach ($emails as $emailString) {
            $email = Email::create($emailString);

            if ($email === null) {
                continue;
            }

            if ($this->dnsEmailsVerifier->verify($email)) {
                $verified[] = $email->getValue();
            }
        }

        return $verified;
    }
}
