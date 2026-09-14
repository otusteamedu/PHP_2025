<?php

declare(strict_types=1);

namespace UnitTests\hw17\Domain\Shared\Validator;

use App\Domain\Shared\Validator\DnsMxRecordValidator;
use App\Infrastructure\Resolver\DnsResolverInterface;
use PHPUnit\Framework\TestCase;

class DnsMxRecordValidatorTest extends TestCase
{
    /**
     * Если домен имеет MX‑запись, то валидатор возвращает true.
     */
    public function testDomainWithMxRecordReturnsTrue(): void
    {
        $hostname = 'example.com';

        $resolverMock = $this->createMock(DnsResolverInterface::class);
        $resolverMock
            ->expects($this->once())
            ->method('hasMxRecord')
            ->with($hostname)
            ->willReturn(true);

        $validator = new DnsMxRecordValidator($resolverMock);

        $this->assertTrue($validator->isValid($hostname));
    }

    /**
     * Если домен не имеет MX‑записи, то валидатор возвращает false.
     */
    public function testDomainWithoutMxRecordReturnsFalse(): void
    {
        $hostname = 'no-mx.example.com';

        $resolverMock = $this->createMock(DnsResolverInterface::class);
        $resolverMock
            ->expects($this->once())
            ->method('hasMxRecord')
            ->with($hostname)
            ->willReturn(false);

        $validator = new DnsMxRecordValidator($resolverMock);

        $this->assertFalse($validator->isValid($hostname));
    }

    /**
     * Если hostname невалиден, то валидатор сразу возвращает false,
     * и проверка MX‑записи не выполняется.
     */
    public function testInvalidHostnameReturnsFalse(): void
    {
        $invalidHostname = '-bad.domain.com';

        $resolverMock = $this->createMock(DnsResolverInterface::class);
        $resolverMock
            ->expects($this->never())
            ->method('hasMxRecord');

        $validator = new DnsMxRecordValidator($resolverMock);

        $this->assertFalse($validator->isValid($invalidHostname));
    }

    /**
     * Если hostname - пустая строка, то валидатор сразу возвращает false,
     * и проверка MX‑записи не выполняется.
     */
    public function testEmptyHostnameReturnsFalse(): void
    {
        $emptyHostname = '';

        $resolverMock = $this->createMock(DnsResolverInterface::class);
        $resolverMock
            ->expects($this->never())
            ->method('hasMxRecord');

        $validator = new DnsMxRecordValidator($resolverMock);

        $this->assertFalse($validator->isValid($emptyHostname));
    }
}
