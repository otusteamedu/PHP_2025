<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator;

use App\Domain\Email\Email;
use App\Domain\Validator\ValidatorInterface;

class DnsMxValidator implements ValidatorInterface
{
    /**
     * @inheritdoc
     */
    public function isValid(Email $email): bool
    {
        $domain = $email->getDomain();

        if ($domain === null) {
            return false;
        }

        if (function_exists('checkdnsrr')) {
            return checkdnsrr($domain, "MX");
        }

        $records = dns_get_record($domain, DNS_MX);

        return !empty($records);
    }
}
