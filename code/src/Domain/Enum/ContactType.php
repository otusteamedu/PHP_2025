<?php

declare(strict_types=1);

namespace MkdBot\Domain\Enum;

/**
 * Тип контакта: УК (uk) или совет дома (council)
 */
enum ContactType: string
{
    case Uk = 'uk';
    case Council = 'council';
}
