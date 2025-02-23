<?php

namespace app;

class EmailVerifier
{
    private const EMAIL_REGEX = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

    /**
     * @param array $emails
     * @return string
     */
    public function verifyEmails(array $emails): string
    {
        $results = [];

        foreach ($emails as $email) {
            $isValidFormat = false;
            $hasMxRecords = false;

            if ($this->isValidFormat($email)) {
                $isValidFormat = true;
            }

            if ($this->hasMxRecords($email)) {
                $hasMxRecords = true;
            }

            if ($isValidFormat) {
                $resultMessage = "Valid format, " . ($hasMxRecords ? "Has MX records" : "Does not have MX records");
            } else {
                $resultMessage = "Invalid format, " . ($hasMxRecords ? "Has MX records" : "Does not have MX records");
            }

            $results[$email] = [
                'valid_format' => $isValidFormat,
                'has_mx_records' => $hasMxRecords,
                'result' => $resultMessage
            ];
        }

        header('Content-type: application/json');
        return json_encode($results, JSON_UNESCAPED_UNICODE);
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