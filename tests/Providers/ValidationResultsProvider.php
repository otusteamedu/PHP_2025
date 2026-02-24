<?php

namespace EmailsVerifier\Tests\Providers;

use EmailsVerifier\Domain\Email;
use EmailsVerifier\Domain\ValidationError;
use EmailsVerifier\Application\DTO\VerificationResultDTO;

class ValidationResultsProvider
{
    public static function validationCasesProvider(): array
    {
        return [
            'ALL_VALID' => [
                ['a@e.ru', 'b@e.ru'], // emails
                [
                    new VerificationResultDTO(new Email('a@e.ru'), true, []),
                    new VerificationResultDTO(new Email('b@e.ru'), true, []),
                ], // results
                2, // totalCount
                2, // validCount
                0, // invalidCount
            ],
            'ALL_INVALID' => [
                ['c@e.ru'],
                [new VerificationResultDTO(new Email('c@e.ru'), false, [new ValidationError('Ошибка')])],
                1,
                0,
                1,
            ],
            'MIXED' => [
                ['a@e.ru', 'c@e.ru'],
                [
                    new VerificationResultDTO(new Email('a@e.ru'), true, []),
                    new VerificationResultDTO(new Email('c@e.ru'), false, [new ValidationError('Ошибка')]),
                ],
                2,
                1,
                1,
            ],
        ];
    }
}
