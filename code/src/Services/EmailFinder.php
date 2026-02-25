<?php

declare(strict_types=1);

namespace Ak\Hw\Services;

use Ak\Hw\Validation\Email;

class EmailFinder
{
    private Email $emailValidator;

    public function __construct()
    {
        $this->emailValidator = new Email();
    }

    /**
     * @param string $text
     * @return array
     */
    public function getEmailsByText(string $text): array
    {
        $pattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';
        preg_match_all($pattern, $text, $matches);

        $validEmails = [];
        foreach ($matches[0] as $email) {
            if ($this->emailValidator->isValid($email)) {
                $validEmails[] = $email;
            }
        }

        return array_unique($validEmails);
    }
}
