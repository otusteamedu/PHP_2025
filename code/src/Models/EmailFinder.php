<?php

namespace Ak\Hw\Models;

use Ak\Hw\Validation\Email as EmailValidator;
use Exception;

class EmailFinder
{
    /**
     * @param $text
     * @param $strings
     * @param $arEmails
     */

    public string $emailPattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';

    public function getEmailsByText($text): array
    {
        return $this->parseText($text);
    }

    /**
     * @param $text
     * @return array
     */
    private function parseText($text): array
    {
        $emails = array();
        preg_match_all($this->emailPattern, $text, $matches);

        if (empty($matches[0])) {
            throw new \RuntimeException('no emails found', 204);
        }


        $emails = [...$emails, ...$matches[0]];

        $validEmails = $this->getValidEmails($emails);
        if (!$validEmails) {
            throw new \RuntimeException('no valid emails found', 204);
        }

        return $validEmails;
    }

    private function getValidEmails(iterable $emails): array
    {
        $result = array();
        $validator = new EmailValidator();
        foreach ($emails as $email) {
           if($validator->isValid($email, true, true) ) {
               $result[] = $email;
           }
        }

        return $result;
    }
}