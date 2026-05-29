<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Domain\Entity;

use DateTimeImmutable;
use MkdBot\Domain\Entity\ConversationState;
use MkdBot\Domain\Enum\ConversationStep;
use PHPUnit\Framework\TestCase;

/**
 * Тесты сущности ConversationState
 */
class ConversationStateTest extends TestCase
{
    public function testCreateState(): void
    {
        $state = new ConversationState(userId: 12345);

        $this->assertEquals(12345, $state->getUserId());
        $this->assertEquals(ConversationStep::MainMenu, $state->getCurrentStep());
        $this->assertEquals([], $state->getData());
        $this->assertFalse($state->isExpired());
    }

    public function testSetStep(): void
    {
        $state = new ConversationState(userId: 12345);
        $state->setStep(ConversationStep::AwaitingSubject);

        $this->assertEquals(ConversationStep::AwaitingSubject, $state->getCurrentStep());
    }

    public function testSetDatum(): void
    {
        $state = new ConversationState(userId: 12345);
        $state->setDatum('subject', 'Тема предложения');
        $state->setDatum('type', 'feature');

        $this->assertEquals('Тема предложения', $state->getData()['subject']);
        $this->assertEquals('feature', $state->getData()['type']);
    }

    public function testResetState(): void
    {
        $state = new ConversationState(userId: 12345);
        $state->setStep(ConversationStep::AwaitingSubject);
        $state->setDatum('subject', 'Тема');
        $state->reset();

        $this->assertEquals(ConversationStep::MainMenu, $state->getCurrentStep());
        $this->assertEquals([], $state->getData());
    }

    public function testSessionExpiry(): void
    {
        $expiresAt = new DateTimeImmutable('-1 minute');
        $state = new ConversationState(userId: 12345, expiresAt: $expiresAt);

        $this->assertTrue($state->isExpired());
    }

    public function testRenewSession(): void
    {
        $expiresAt = new DateTimeImmutable('-1 minute');
        $state = new ConversationState(userId: 12345, expiresAt: $expiresAt);
        $this->assertTrue($state->isExpired());

        $state->renew();
        $this->assertFalse($state->isExpired());
    }

    /**
     * getUpdatedAt() — возвращает время обновления
     */
    public function testGetUpdatedAtReturnsDateTime(): void
    {
        $state = new ConversationState(userId: 12345);
        $this->assertInstanceOf(DateTimeImmutable::class, $state->getUpdatedAt());
    }

    /**
     * getExpiresAt() — возвращает время истечения сессии
     */
    public function testGetExpiresAtReturnsDateTime(): void
    {
        $state = new ConversationState(userId: 12345);
        $this->assertInstanceOf(DateTimeImmutable::class, $state->getExpiresAt());
    }

    /**
     * setData() — заменяет все данные и обновляет время
     */
    public function testSetDataReplacesAllData(): void
    {
        $state = new ConversationState(userId: 12345);
        $state->setDatum('old_key', 'old_value');

        $state->setData(['new_key' => 'new_value']);

        $this->assertSame(['new_key' => 'new_value'], $state->getData());
        $this->assertArrayNotHasKey('old_key', $state->getData());
    }

    /**
     * setStep() — возвращает self для цепочки вызовов
     */
    public function testSetStepReturnsSelf(): void
    {
        $state = new ConversationState(userId: 12345);
        $result = $state->setStep(ConversationStep::AwaitingSubject);

        $this->assertSame($state, $result);
    }

    /**
     * setData() — возвращает self для цепочки вызовов
     */
    public function testSetDataReturnsSelf(): void
    {
        $state = new ConversationState(userId: 12345);
        $result = $state->setData(['key' => 'value']);

        $this->assertSame($state, $result);
    }

    /**
     * setDatum() — возвращает self для цепочки вызовов
     */
    public function testSetDatumReturnsSelf(): void
    {
        $state = new ConversationState(userId: 12345);
        $result = $state->setDatum('key', 'value');

        $this->assertSame($state, $result);
    }

    /**
     * reset() — возвращает self для цепочки вызовов
     */
    public function testResetReturnsSelf(): void
    {
        $state = new ConversationState(userId: 12345);
        $result = $state->reset();

        $this->assertSame($state, $result);
    }

    /**
     * renew() — возвращает self для цепочки вызовов
     */
    public function testRenewReturnsSelf(): void
    {
        $state = new ConversationState(userId: 12345);
        $result = $state->renew();

        $this->assertSame($state, $result);
    }
}
