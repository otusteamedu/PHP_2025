<?php

declare(strict_types=1);

namespace MkdBot\Domain\Enum;

/**
 * Шаг многошагового диалога
 */
enum ConversationStep: string
{
    case MainMenu = 'main_menu';
    case AwaitingSubject = 'awaiting_subject';
    case AwaitingDescription = 'awaiting_description';
    case Preview = 'preview';
    case AwaitingQuestion = 'awaiting_question'; // v2: для RAG
}
