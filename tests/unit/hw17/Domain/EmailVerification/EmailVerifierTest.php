<?php

declare(strict_types=1);

namespace UnitTests\hw17\Domain\EmailVerification;

use App\Domain\EmailVerification\EmailVerifier;
use App\Domain\Shared\Validator\EmailValidator;
use PHPUnit\Framework\TestCase;

class EmailVerifierTest extends TestCase
{
    /**
     * Если массив emails пустой, то возвращается пустой массив,
     * и валидатор не вызывается ни разу.
     */
    public function testEmptyArrayReturnsEmpty(): void
    {
        $validatorMock = $this->createMock(EmailValidator::class);
        $validatorMock
            ->expects($this->never())
            ->method('isValid');

        $verifier = new EmailVerifier($validatorMock);

        $result = $verifier->getValidEmails([]);

        $this->assertEquals([], $result);
    }

    /**
     * Если все emails в массиве валидны, то возвращаются все элементы массива.
     */
    public function testAllValidEmailsReturnsAll(): void
    {
        $emails = ['first@example.com', 'second@mail.ru'];

        $validatorMock = $this->createMock(EmailValidator::class);
        $validatorMock
            ->expects($this->exactly(count($emails)))
            ->method('isValid')
            ->willReturn(true);

        $verifier = new EmailVerifier($validatorMock);
        $result = $verifier->getValidEmails($emails);

        $this->assertEquals($emails, $result);
    }

    /**
     * Если все emails в массиве невалидны, то возвращается пустой массив.
     */
    public function testAllInvalidEmailsReturnsEmpty(): void
    {
        $emails = ['bad-email', 'no-at-sign', '@no-local-part'];

        $validatorMock = $this->createMock(EmailValidator::class);
        $validatorMock
            ->expects($this->exactly(count($emails)))
            ->method('isValid')
            ->willReturn(false);

        $verifier = new EmailVerifier($validatorMock);
        $result = $verifier->getValidEmails($emails);

        $this->assertEquals([], $result);
    }

    /**
     * Если массив содержит смешанные email‑адреса, то возвращаются только валидные,
     * порядок сохраняется, а ключи массива пересчитываются последовательно.
     */
    public function testMixedArrayReturnsOnlyValidWithSequentialKeys(): void
    {
        $emails = [
            'valid@example.com',
            'invalid-no-at-sign',
            'another@valid.net',
        ];
        $parameterSets = array_map(fn(string $email) => [$email], $emails);

        $validatorMock = $this->createMock(EmailValidator::class);
        $validatorMock
            ->expects($this->exactly(count($emails)))
            ->method('isValid')
            ->withParameterSetsInOrder(...$parameterSets)
            ->willReturnOnConsecutiveCalls(true, false, true);

        $verifier = new EmailVerifier($validatorMock);
        $result = $verifier->getValidEmails($emails);

        $this->assertEquals(['valid@example.com', 'another@valid.net'], $result);
        $this->assertEquals([0, 1], array_keys($result));
    }
}
