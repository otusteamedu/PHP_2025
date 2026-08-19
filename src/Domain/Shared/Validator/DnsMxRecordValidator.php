<?php

declare(strict_types=1);

namespace App\Domain\Shared\Validator;

use App\Infrastructure\Resolver\DnsResolverInterface;

class DnsMxRecordValidator
{
    public function __construct(
        private readonly DnsResolverInterface $resolver,
    ) {
    }

    public function isValid(string $hostname): bool
    {
        if ($hostname === '') {
            return false;
        }

        $asciiHostname = idn_to_ascii($hostname);
        if ($asciiHostname === false) {
            return false;
        }

        return $this->resolver->hasMxRecord($asciiHostname);
    }
}
