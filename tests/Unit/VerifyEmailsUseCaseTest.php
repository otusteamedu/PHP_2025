<?php

namespace EmailsVerifier\Tests\Unit;

use EmailsVerifier\Domain\ValidationError;
use EmailsVerifier\Application\Interfaces\ValidationStrategyInterface;
use EmailsVerifier\Application\UseCases\VerifyEmailsUseCase;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class VerifyEmailsUseCaseTest extends TestCase
{
    private ValidationStrategyInterface&MockObject $validatorMock;
    private VerifyEmailsUseCase $useCase;

    protected function setUp(): void
    {
        $this->validatorMock = $this->createMock(ValidationStrategyInterface::class);
        $this->useCase = new VerifyEmailsUseCase($this->validatorMock);
    }

    public function testExecuteAllValid(): void
    {
        $this->validatorMock->method('validate')->willReturn([]);

        $results = $this->useCase->execute(['a@test.ru', 'b@test.ru']);

        $this->assertCount(2, $results);
        $this->assertSame(2, $this->countValid($results));
        $this->assertSame(0, $this->countErrors($results));
    }

    public function testExecuteAllInvalid(): void
    {
        $this->validatorMock->method('validate')->willReturn([new ValidationError('Ошибка')]);

        $results = $this->useCase->execute(['bad@test.ru']);

        $this->assertCount(1, $results);
        $this->assertSame(0, $this->countValid($results));
        $this->assertSame(1, $this->countErrors($results));
    }

    public function testExecuteMixed(): void
    {
        $this->validatorMock->method('validate')->willReturnCallback(
            fn($email) => $email->getAddress() === 'bad@test.ru'
                ? [new ValidationError('Ошибка 1'), new ValidationError('Ошибка 2')]
                : []
        );

        $results = $this->useCase->execute(['good@test.ru', 'bad@test.ru']);

        $this->assertCount(2, $results);
        $this->assertSame(1, $this->countValid($results));
        $this->assertSame(2, $this->countErrors($results));
    }

    private function countValid(array $results): int
    {
        return count(array_filter($results, fn($r) => $r->isValid));
    }

    private function countErrors(array $results): int
    {
        return array_sum(array_map(fn($r) => count($r->errors), $results));
    }
}
