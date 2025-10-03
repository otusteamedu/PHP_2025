<?php
declare(strict_types=1);

namespace App\tests {

use App\Application\WithContainer;
use App\Validators\EmailValidator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EmailValidatorTest extends TestCase
{
    use WithContainer;

    private function sut(): EmailValidator
    {
        return $this->createFromContainer(EmailValidator::class);
    }

    #[Test]
    public function test_valid_email_without_mx_check_returns_true(): void
    {
        $this->assertTrue( $this->sut()->validate('user@example.com'));
    }

    #[Test]
    public function test_invalid_emails_return_false(): void
    {
        $validator = $this->sut();
        $this->assertFalse($validator->validate('plainaddress'));
        $this->assertFalse($validator->validate('invalid@'));
        $this->assertFalse($validator->validate('@invalid'));
        $this->assertFalse($validator->validate('foo@bar..com'));
        $this->assertFalse($validator->validate('bad domain@example.com'));
        $this->assertFalse($validator->validate(''));
    }

    #[Test]
    public function test_with_mx_record_true_returns_true_when_dns_has_mx(): void
    {
        $this->assertTrue($this->sut()->validate('user@gmail.com', true));
    }

    #[Test]
    public function test_with_mx_record_true_returns_false_when_dns_has_no_mx(): void
    {
        $this->assertFalse($this->sut()->validate('user@some.test', true));
    }
}
}
