<?php

namespace EmailsVerifier\Tests\Unit;

use EmailsVerifier\Domain\Email;
use EmailsVerifier\Domain\Interfaces\MxCheckerInterface;
use EmailsVerifier\Domain\Validators\MxValidator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use EmailsVerifier\Tests\ValidationErrorHelper;

class MxValidatorTest extends TestCase
{
    private function createMxCheckerMock(bool $hasMxRecord): MxCheckerInterface
    {
        $mock = $this->createMock(MxCheckerInterface::class);
        $mock->method('hasMxRecord')->willReturn($hasMxRecord);
        return $mock;
    }

    #[DataProvider('mxValidationProvider')]
    public function testMxValidation(string $email, bool $mxResult, ?string $expectedError = null): void
    {
        $validator = new MxValidator($this->createMxCheckerMock($mxResult));
        $errors = $validator->validate(new Email($email));

        if ($expectedError === null) {
            $this->assertEmpty($errors);
        } else {
            $this->assertContains($expectedError, ValidationErrorHelper::getErrorMessages($errors));
        }
    }

    public static function mxValidationProvider(): array
    {
        return [
            'VALID_DOMAIN' => ['test@mail.ru', true],
            'INVALID_DOMAIN' => ['test@invalid-domain-abc.ru', false, 'MX-запись не найдена'],
            'EMPTY_DOMAIN' => ['test@', false, 'Домен не может быть пустым'],
            'NO_AT_SYMBOL' => ['invalid', false, 'Некорректное количество символов @'],
        ];
    }
}
