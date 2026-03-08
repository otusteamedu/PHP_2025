<?php

declare(strict_types=1);

namespace Otus\Queue\Tests\Domain\Validator;

use Otus\Queue\Domain\Entity\Message;
use Otus\Queue\Domain\Exception\ValidationException;
use Otus\Queue\Domain\Validator\MessageValidator;
use PHPUnit\Framework\TestCase;

final class MessageValidatorTest extends TestCase
{
    public function testValidatePassesForNonEmptyAuthorAndText(): void
    {
        $validator = new MessageValidator(new Message('alex', 'hello', 123));

        $validator->validate();

        self::assertTrue(true);
    }

    public function testValidateThrowsExceptionWithAllErrors(): void
    {
        $validator = new MessageValidator(new Message('   ', '', 123));

        try {
            $validator->validate();
            self::fail('ValidationException was not thrown');
        } catch (ValidationException $exception) {
            self::assertSame('Validation failed', $exception->getMessage());
            self::assertSame(
                [
                    'author' => 'Author is required',
                    'text' => 'Text is required',
                ],
                $exception->getErrors()
            );
        }
    }
}
