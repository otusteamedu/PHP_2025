<?php

declare(strict_types=1);

namespace unit\Infrastructure;

use App\Domain\Email;
use App\Infrastructure\DnsEmailVerifier;
use Codeception\Test\Unit;

final class DnsEmailVerifierTest extends Unit
{
    public function testVerify(): void
    {
        $email = Email::create('test@example.com');
        $verifier = new DnsEmailVerifier();

        $this->assertTrue($verifier->verify($email));
    }
}
