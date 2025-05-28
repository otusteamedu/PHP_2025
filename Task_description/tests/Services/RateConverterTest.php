<?php

declare(strict_types=1);

namespace Tests\Services;

use App\DummyRateRepository;
use App\Exception\RateConverterException;
use App\RateRepositoryInterface;
use App\Services\RateConverter;
use Mockery as m;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RateConverterTest extends TestCase
{
    protected ?RateRepositoryInterface $rateRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rateRepository = new DummyRateRepository();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->rateRepository = null;
    }

    #[DataProvider('getRateDataProvider')]
    public function testGetRate(int $amount, string $from, string $to, int $expectedRate): void
    {
        $rateRepositoryMock = $this->createMock(RateRepositoryInterface::class);
        $rateRepositoryMock
            ->expects($this->atMost(2))
            ->method('getRate')
            ->with($from, $to)
            ->willReturn(10);

        $rateRepositoryMock = m::mock(DummyRateRepository::class)
            ->shouldReceive('getRate')
            ->with($from, $to)
            ->andReturn(10)
            ->getMock();

        // arrange
        $rateConverter = new RateConverter($rateRepositoryMock);

        // act
        $result = $rateConverter->getRate($amount, $from, $to);

        // assert
        $this->assertEquals($expectedRate, $result);
    }

    public function testGetRateNegative(): void
    {
        $this->expectException(RateConverterException::class);
        $this->expectExceptionMessage('Unsupported currency');

        $rateConverter = new RateConverter($this->rateRepository);
        $result = $rateConverter->getRate(10, 'CNY', 'USD');
    }

    public static function getRateDataProvider(): array
    {
        return [
            [10, 'RUB', 'USD', 100],
            [20, 'RUB', 'USD', 200],
        ];
    }
}