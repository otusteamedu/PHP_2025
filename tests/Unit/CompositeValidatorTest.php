<?php

namespace EmailsVerifier\Tests\Unit;

use EmailsVerifier\Domain\Email;
use EmailsVerifier\Domain\ValidationError;
use EmailsVerifier\Application\Services\CompositeValidator;
use EmailsVerifier\Application\Interfaces\ValidationStrategyInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class CompositeValidatorTest extends TestCase
{
    private function createValidationStrategyMock(array $errors = []): ValidationStrategyInterface
    {
        $mock = $this->createMock(ValidationStrategyInterface::class);
        $mock->method('validate')->willReturn($errors);
        return $mock;
    }

    #[DataProvider('compositeProvider')]
    public function testCompositeValidation(array $strategiesErrors, int $expectedCount): void
    {
        $strategies = [];
        foreach ($strategiesErrors as $errors) {
            $strategies[] = $this->createValidationStrategyMock($errors);
        }

        $validator = new CompositeValidator($strategies);
        $errors = $validator->validate(new Email('test@example.ru'));

        $this->assertCount($expectedCount, $errors);
    }

    public static function compositeProvider(): array
    {
        return [
            'ALL_PASS' => [
                [[], []],
                0,
            ],
            'ALL_FAIL' => [
                [[new ValidationError('Ошибка 1')], [new ValidationError('Ошибка 2')]],
                2,
            ],
            'MIXED_2_STRATEGIES' => [
                [[new ValidationError('Ошибка 1')], []],
                1,
            ],
            'MIXED_3_STRATEGIES' => [
                [
                    [new ValidationError('Ошибка 1')],
                    [],
                    [new ValidationError('Ошибка 2'), new ValidationError('Ошибка 3')],
                ],
                3,
            ],
            'MULTIPLE_ERRORS_1_STRATEGY' => [
                [[new ValidationError('Ошибка 1'), new ValidationError('Ошибка 2')], []],
                2,
            ],
        ];
    }
}
