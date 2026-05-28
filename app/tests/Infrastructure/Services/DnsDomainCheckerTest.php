<?php
namespace Pryaniki\App\Infrastructure\Services;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DnsDomainCheckerTest extends TestCase
{
    #[DataProvider('validDomainDataProvider')]
    public function testValidDomain(string $domain): void
    {
        self::assertTrue(
            (new DnsDomainChecker())->isExistsDomain($domain),
            sprintf('Domain "%s" does not exist', $domain),
        );
    }

    public static function validDomainDataProvider(): array
    {
        return [
            ['yandex.ru'],
            ['ya.ru'],
            ['yandx.ru'],
            ['gmail.com'],
        ];
    }

    #[DataProvider('invalidDomainDataProvider')]
    public function testInvalidDomain(string $domain): void
    {
        self::assertFalse(
            (new DnsDomainChecker())->isExistsDomain($domain),
            sprintf('Domain "%s" exist', $domain),
        );
    }

    public static function invalidDomainDataProvider(): array
    {
        return [
            ['gmal.com'],
            ['yan1dx.ru'],
            ['test@test.ru'],
            [''],
        ];
    }
}