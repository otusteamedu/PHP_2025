<?php

namespace App\Services;

class EmailService
{
    /**
     * @param string $string
     * @return string[]
     */
    public function parseEmails(string $string): array
    {
        $rawEmails = $this->extractFromString($string);
        $validEmails = [];
        foreach ($rawEmails as $rawEmail) {
            if ($this->isValid($rawEmail)) {
                $validEmails[] = $rawEmail;
            }
        }
        return $validEmails;
    }

    private function extractFromString(string $string): array
    {
        $pattern = '/([a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~.-]+)@([a-zA-Z0-9.-]+)/u';
        preg_match_all($pattern, $string, $matches);

        return $matches[0] ?? [];
    }

    public function isValid(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        } elseif (!$this->checkMXRecord($email)) {
            return false;
        }
        return true;
    }

    private function checkMXRecord(string $email): bool
    {
        [, $domain] = explode('@', $email);
        if (function_exists('getmxrr')) {
            if (getmxrr($domain, $mxHosts)) {
                return true;
            }
        } else {
            $records = dns_get_record($domain, DNS_MX);
            if (!empty($records)) {
                return true;
            }
        }

        return checkdnsrr($domain, 'MX');
    }

    public function parseEmail(string $string): ?string
    {
        if (!($emails = $this->extractFromString($string))) {
            return null;
        } elseif ($this->isValid($emails[0])) {
            return $emails[0];
        }

        return null;
    }
}