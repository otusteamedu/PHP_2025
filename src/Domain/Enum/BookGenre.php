<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum BookGenre: string
{
    case RomanceNovel = 'Любовный роман';
    case DetectiveNovel = 'Детектив';
    case Art = 'Искусство';
    case HistoricalNovel = 'Исторический роман';
    case ChildrenLiterature = 'Детская литература';
    case ScienceFiction = 'Фантастика';
    case Gardening = 'Сад и огород';
}
