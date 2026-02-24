<?php

namespace EmailsVerifier\Presentation\Services;

use EmailsVerifier\Application\DTO\VerificationResultDTO;

readonly class VerificationResultProcessor
{
    public static function processResults(array $results): array
    {
        $validEmails = array_filter($results, function (VerificationResultDTO $result) {
            return $result->isValid;
        });

        $invalidEmails = array_filter($results, function (VerificationResultDTO $result) {
            return !$result->isValid;
        });

        return [
            'results' => $results,
            'valid_emails' => $validEmails,
            'invalid_emails' => $invalidEmails,
            'total_count' => count($results),
            'valid_count' => count($validEmails),
            'invalid_count' => count($invalidEmails)
        ];
    }
}
