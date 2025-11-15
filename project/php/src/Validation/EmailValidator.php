<?php

namespace App\Validation;

use App\Interface\EmailValidatorInterface;

class EmailValidator implements EmailValidatorInterface {
    private const EMAIL_REGEX = '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/';

    public function verifyEmails(array $emails): array {
        $results = [];
        foreach ($emails as $email) {
            $email = trim($email);
            $result = [
                'email' => htmlspecialchars($email, ENT_QUOTES, 'UTF-8'),
                'is_valid' => false,
                'message' => ''
            ];

            try {
                if (!$this->isValidSyntax($email)) {
                    $result['message'] = 'Invalid email format';
                    $results[] = $result;
                    continue;
                }

                $domain = substr(strrchr($email, '@'), 1);
                if (!$this->hasValidMxRecord($domain)) {
                    $result['message'] = 'No valid MX record found for domain';
                    $results[] = $result;
                    continue;
                }

                $result['is_valid'] = true;
                $result['message'] = 'Email is valid';
            } catch (\Exception $e) {
                $result['message'] = 'Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
            }

            $results[] = $result;
        }
        return $results;
    }

    private function isValidSyntax(string $email): bool {
        return preg_match(self::EMAIL_REGEX, $email) === 1;
    }

    private function hasValidMxRecord(string $domain): bool {
        return checkdnsrr($domain, 'MX');
    }
}