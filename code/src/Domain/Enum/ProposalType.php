<?php

declare(strict_types=1);

namespace MkdBot\Domain\Enum;

/**
 * Тип предложения: новый функционал (feature) или предложение совету (suggestion)
 */
enum ProposalType: string
{
    case Feature = 'feature';
    case Suggestion = 'suggestion';
}
