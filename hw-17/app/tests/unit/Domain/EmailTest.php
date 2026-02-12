<?php

declare(strict_types=1);

namespace unit\Domain;

use App\Domain\Email;
use Codeception\Test\Unit;

final class EmailTest extends Unit
{
    public function testCreateSuccess(): void
    {
        $emailValue = 'test@example.com';
        $email = Email::create($emailValue);

        $this->assertSame($emailValue, $email->getValue());
    }

    public function testCreateNotValid(): void
    {
        $emailValue = 'testexample.com';
        $email = Email::create($emailValue);

        $this->assertSame(null, $email);
    }
}
