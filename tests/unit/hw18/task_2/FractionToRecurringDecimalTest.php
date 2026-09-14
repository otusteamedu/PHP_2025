<?php

declare(strict_types=1);

namespace UnitTests\hw18\task_2;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../../algo/hw18/task_2/FractionToRecurringDecimal.php';

class FractionToRecurringDecimalTest extends TestCase
{
    private \FractionToRecurringDecimal $converter;

    protected function setUp(): void
    {
        $this->converter = new \FractionToRecurringDecimal();
    }

    #[DataProvider('provideFractions')]
    public function testReturnsStringRepresentationWhenConvertingFraction(
        int $numerator,
        int $denominator,
        string $expected,
    ): void {
        $result = $this->converter->fractionToDecimal($numerator, $denominator);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array<string, array{int, int, string}>
     */
    public static function provideFractions(): array
    {
        return [
            // кейсы с leetcode
            'finite decimal fraction' => [1, 2, '0.5'],
            'integer result with denominator one' => [2, 1, '2'],
            'repeating period from start' => [4, 333, '0.(012)'],

            // дополнительные кейсы
            'zero numerator' => [0, 2, '0'],
            'integer result with non-trivial denominator' => [50, 5, '10'],
            'non-repeating part before period' => [1, 6, '0.1(6)'],
            'zero digit before period' => [1, 15, '0.0(6)'],
            'both negative operands' => [-6, -7, '0.(857142)'],
            'negative numerator, positive denominator' => [-1, 2, '-0.5'],
            'positive numerator, negative denominator' => [1, -2, '-0.5'],
            'negative result with long period' => [-1, 7777, '-0.(000128584287)'],
            'max int32 numerator' => [2147483647, 1, '2147483647'],
            'min int32 numerator' => [-2147483648, 1, '-2147483648'],
            'large operands, finite decimal' => [2147483647, 200, '10737418.235'],
        ];
    }
}
