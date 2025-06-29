<?php

declare(strict_types=1);

namespace App\Application\NewsContentModifier;

readonly class DummyNewsContentModifier implements NewsContentModifierInterface
{
    public function modify(string $content): string
    {
        return $content;
    }
}
