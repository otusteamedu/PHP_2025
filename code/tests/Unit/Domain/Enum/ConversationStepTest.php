<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Domain\Enum;

use MkdBot\Domain\Enum\ConversationStep;
use PHPUnit\Framework\TestCase;

/**
 * Тесты Enum ConversationStep
 */
class ConversationStepTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertEquals('main_menu', ConversationStep::MainMenu->value);
        $this->assertEquals('awaiting_subject', ConversationStep::AwaitingSubject->value);
        $this->assertEquals('awaiting_description', ConversationStep::AwaitingDescription->value);
        $this->assertEquals('preview', ConversationStep::Preview->value);
        $this->assertEquals('awaiting_question', ConversationStep::AwaitingQuestion->value);
    }

    public function testFromString(): void
    {
        $this->assertEquals(ConversationStep::MainMenu, ConversationStep::from('main_menu'));
        $this->assertEquals(ConversationStep::AwaitingSubject, ConversationStep::from('awaiting_subject'));
    }
}
