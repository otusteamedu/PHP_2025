<?php

namespace Alisaselezneva\Code\Domain\Services;

use Alisaselezneva\Code\Domain\Services\Checkers\DnsMxRecordChecker;
use Alisaselezneva\Code\Domain\Services\Checkers\EmailFormatChecker;
use Alisaselezneva\Code\Domain\Services\Checkers\EmailFormatCheckerInterface;
use Alisaselezneva\Code\Domain\Services\Checkers\MxRecordCheckerInterface;

class EmailVerifier
{
    public function verify(string $email): VerificationResult
    {
        $checkers = [
            'format' => fn (string $value): bool => $this->getFormatChecker()->isValid($value),
            'mx_records' => fn (string $value): bool => $this->getMxRecordChecker()->hasMxRecords($value),
        ];

        $checks = [];

        foreach ($checkers as $name => $checker) {
            $checks[$name] = $checker($email);

            if ($checks[$name] === false) {
                break;
            }
        }

        $isValid = ($checks['format'] ?? false) && ($checks['mx_records'] ?? false);

        return new VerificationResult($isValid, $checks);
    }

    public function massVerify(array $emails): array
    {
        $results = [];

        foreach ($emails as $email) {
            $results[] = $this->verify($email);
        }

        return $results;
    }

    protected function getFormatChecker(): EmailFormatCheckerInterface
    {
        return new EmailFormatChecker();
    }

    protected function getMxRecordChecker(): MxRecordCheckerInterface
    {
        return new DnsMxRecordChecker();
    }
}
