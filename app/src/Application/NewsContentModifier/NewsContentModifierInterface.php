<?php

declare(strict_types=1);

namespace App\Application\NewsContentModifier;

interface NewsContentModifierInterface
{
    public function modify(string $content): string;
}
