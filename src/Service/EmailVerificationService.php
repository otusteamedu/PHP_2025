<?php
declare(strict_types=1);

namespace App\Service;

use App\Validator\EmailValidator;

class EmailVerificationService
{
    private EmailValidator $emailValidator;

    /**
     * @param EmailValidator $emailValidator
     */
    public function __construct(EmailValidator $emailValidator)
    {
        $this->emailValidator = $emailValidator;
    }

    /**
     * @param array $emails
     * @return array{email: string, format_valid: bool, mx_record: bool, valid: bool, reason: string}[]
     */
    public function verifyMultiple(array $emails): array
    {
        $verifiedEmails = [];
        foreach ($emails as $email) {
            if (trim($email) === '') {
                continue;
            }

            $verifiedEmails[] = $this->verify($email);
        }

        return $verifiedEmails;
    }

    /**
     * @param string $email
     * @return array
     */
    public function verify(string $email): array
    {
        $formatValid = $this->emailValidator->isValidFormat($email);
        $mxValid = $formatValid && $this->emailValidator->hasMxRecord($email);

        return [
            'email' => $email,
            'format_valid' => $formatValid,
            'mx_record' => $mxValid,
            'valid' => $formatValid && $mxValid,
            'reason' => $this->getReason($formatValid, $mxValid)
        ];
    }

    /**
     * @param bool $formatValid
     * @param bool $mxValid
     * @return string
     */
    private function getReason(bool $formatValid, bool $mxValid): string
    {
        if (!$formatValid) {
            return 'Invalid format';
        }

        if (!$mxValid) {
            return 'No MX record';
        }

        return 'Valid';
    }
}
