<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Domain\Entity;

use DateTimeImmutable;
use MkdBot\Domain\Entity\Proposal;
use MkdBot\Domain\Enum\ProposalType;
use PHPUnit\Framework\TestCase;

/**
 * Тесты сущности Proposal
 */
class ProposalTest extends TestCase
{
    public function testCreateFeatureProposal(): void
    {
        $proposal = new Proposal(
            type: ProposalType::Feature,
            userId: 12345,
            userName: 'Тестовый пользователь',
            subject: 'Новая функция',
            content: 'Описание новой функции',
        );

        $this->assertNull($proposal->getId());
        $this->assertEquals(ProposalType::Feature, $proposal->getType());
        $this->assertEquals(12345, $proposal->getUserId());
        $this->assertEquals('Тестовый пользователь', $proposal->getUserName());
        $this->assertEquals('Новая функция', $proposal->getSubject());
        $this->assertEquals('Описание новой функции', $proposal->getContent());
        $this->assertInstanceOf(DateTimeImmutable::class, $proposal->getCreatedAt());
    }

    public function testCreateSuggestionProposal(): void
    {
        $proposal = new Proposal(type: ProposalType::Suggestion);
        $this->assertEquals(ProposalType::Suggestion, $proposal->getType());
    }

    public function testSubjectValidation(): void
    {
        $proposal = new Proposal(subject: 'Короткая тема');
        $this->assertTrue($proposal->isSubjectValid());

        $proposal = new Proposal(subject: '');
        $this->assertFalse($proposal->isSubjectValid());

        $longSubject = str_repeat('а', 201);
        $proposal = new Proposal(subject: $longSubject);
        $this->assertFalse($proposal->isSubjectValid());

        $proposal = new Proposal(subject: str_repeat('а', 200));
        $this->assertTrue($proposal->isSubjectValid());
    }

    public function testContentValidation(): void
    {
        $proposal = new Proposal(content: 'Короткое описание');
        $this->assertTrue($proposal->isContentValid());

        $proposal = new Proposal(content: '');
        $this->assertFalse($proposal->isContentValid());

        $longContent = str_repeat('а', 3001);
        $proposal = new Proposal(content: $longContent);
        $this->assertFalse($proposal->isContentValid());

        $proposal = new Proposal(content: str_repeat('а', 3000));
        $this->assertTrue($proposal->isContentValid());
    }
}
