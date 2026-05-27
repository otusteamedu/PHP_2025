<?php

namespace Pryaniki\App\Application\DTO;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ValidateEmailRequestDTOTest extends TestCase
{
    #[DataProvider('emailDataProvider')]
    public function testCreateDTOObject(string $email): void
    {
        $dto = new ValidateEmailRequestDTO($email);
        self::assertSame($dto->email, $email);
    }

    public static function emailDataProvider() : array
    {
        return [
            ['yandex.ru'],
            ['ya.ru'],
            ['yandx.ru'],
            ['gmail.com'],
        ];
    }
}