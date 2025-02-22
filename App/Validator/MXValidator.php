<?php

declare(strict_types=1);

namespace App\Validator;

class MXValidator
{
    public function __invoke(string $domain): bool
    {
        return \checkdnsrr($domain);
    }
}
