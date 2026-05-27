<?php

namespace Pryaniki\App\Application\DTO;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ValidationEmailResponseDTOTest extends TestCase
{
    #[DataProvider('responseDataProvider')]
    public function testCreateDTOObject(bool $success, array $errors): void
    {
        $dto = new ValidationEmailResponseDTO($success, $errors);

        self::assertSame($dto->success, $success);
        self::assertSame($dto->error, $errors);

        self::assertCount(count($errors), $dto->error, 'The number of errors in DTO not match the transmitted number of errors');
    }

    public static function responseDataProvider(): array
    {
        return [
            [true, []],
            [
                false,
                [
                    'error'
                ]
            ],
            [
                false,
                [
                    'error1',
                    'error2',
                ]
            ],
            [
                false,
                []
            ],
        ];
    }
}