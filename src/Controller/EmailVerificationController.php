<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\EmailVerificationService;
use App\Validator\EmailValidator;

class EmailVerificationController
{
    private EmailVerificationService $emailVerificationService;

    /**
     * @param EmailVerificationService $emailVerificationService
     */
    public function __construct(EmailVerificationService $emailVerificationService)
    {
        $this->emailVerificationService = $emailVerificationService;
    }

    /**
     * @param string[] $emails
     * @return array{email: string, format_valid: bool, mx_record: bool, valid: bool, reason: string}[]
     */
    public function verifyEmails(array $emails): array
    {
        $filteredEmails = array_filter($emails, 'is_string');
        return $this->emailVerificationService->verifyMultiple($filteredEmails);
    }

    /**
     * @return self
     */
    public static function create(): self
    {
        return new self(new EmailVerificationService(new EmailValidator()));
    }
}
