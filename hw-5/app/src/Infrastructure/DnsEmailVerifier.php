<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\Email;
use App\Domain\EmailVerifiedInterface;

final readonly class DnsEmailVerifier implements EmailVerifiedInterface
{
    public function verify(Email $email): bool
    {
        $domain = substr(strrchr($email->getValue(), '@'), 1);

        return checkdnsrr($domain);
    }
}
