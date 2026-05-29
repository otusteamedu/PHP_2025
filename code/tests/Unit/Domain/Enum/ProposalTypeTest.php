<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Domain\Enum;

use MkdBot\Domain\Enum\ProposalType;
use PHPUnit\Framework\TestCase;
use ValueError;

/**
 * Тесты Enum ProposalType
 */
class ProposalTypeTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertEquals('feature', ProposalType::Feature->value);
        $this->assertEquals('suggestion', ProposalType::Suggestion->value);
    }

    public function testFromString(): void
    {
        $this->assertEquals(ProposalType::Feature, ProposalType::from('feature'));
        $this->assertEquals(ProposalType::Suggestion, ProposalType::from('suggestion'));
    }

    public function testInvalidValue(): void
    {
        $this->expectException(ValueError::class);
        ProposalType::from('invalid');
    }
}
