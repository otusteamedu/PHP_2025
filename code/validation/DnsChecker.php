<?php

namespace App\validation;

class DnsChecker {
    public function hasMx(string $domain): bool {
        return checkdnsrr($domain, 'MX');
    }
}