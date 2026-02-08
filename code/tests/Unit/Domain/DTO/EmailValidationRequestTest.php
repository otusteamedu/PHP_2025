<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\DTO;

use App\Domain\DTO\EmailValidationRequest;
use PHPUnit\Framework\TestCase;

class EmailValidationRequestTest extends TestCase
{
    public function testSetEmails(): void
    {
        $emails = ['test@example.com', 'user@domain.org'];
        $request = new EmailValidationRequest($emails);

        $this->assertCount(2, $request->emails);
        $this->assertSame($emails, $request->emails);
    }

    public function testSetEmailsWithSingleEmail(): void
    {
        $emails = ['single@test.com'];
        $request = new EmailValidationRequest($emails);

        $this->assertCount(1, $request->emails);
        $this->assertSame('single@test.com', $request->emails[0]);
    }

    public function testSetEmailsWithEmptyArray(): void
    {
        $emails = [];
        $request = new EmailValidationRequest($emails);

        $this->assertCount(0, $request->emails);
        $this->assertSame([], $request->emails);
    }
}
