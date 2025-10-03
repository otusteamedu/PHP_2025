<?php
declare(strict_types=1);

namespace App\tests;

use App\Application\WithContainer;
use App\Validators\EmailValidator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class WithContainerTest extends TestCase
{
    use WithContainer;

    #[Test]
    public function test_create_from_container_resolves_email_validator(): void
    {
        $validator = $this->createFromContainer(EmailValidator::class);
        $this->assertInstanceOf(EmailValidator::class, $validator);
    }

    #[Test]
    public function test_inject_in_container_does_not_throw(): void
    {
        $this->injectInContainer('some.param', 42);
        $this->injectInContainer('another.param', 'value');
        $this->assertTrue(true);
    }
}
