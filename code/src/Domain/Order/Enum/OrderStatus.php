<?php

declare(strict_types=1);

namespace Otus\Code\Domain\Order\Enum;

enum OrderStatus: string
{
    case Draft = 'draft';
    case Created = 'created';
    case Cooking = 'cooking';
    case Ready = 'ready';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
