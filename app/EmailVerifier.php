<?php

namespace app;

class EmailVerifier
{
    private const EMAIL_REGEX = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

    /**
     * @param array $emails
     * @return array
     */
    public function verifyEmails(array $emails): array
    {
        $results = [];

        foreach ($emails as $email) {
            $isValidFormat = $this->isValidFormat($email);
            $hasMxRecords = $this->hasMxRecords($email);

            $results[$email] = [
                'valid_format' => $isValidFormat,
                'has_mx_records' => $hasMxRecords
            ];
        }

        return $results;
    }

    /**
     * @param string $email
     * @return bool
     */
    private function isValidFormat(string $email): bool
    {
        return preg_match(self::EMAIL_REGEX, $email) === 1;
    }

    /**
     * @param string $email
     * @return bool
     */
    private function hasMxRecords(string $email): bool
    {
        $domain = substr(strrchr($email, "@"), 1);

        if ($domain) {
            return checkdnsrr($domain, 'MX');
        }

        return false;
    }
}