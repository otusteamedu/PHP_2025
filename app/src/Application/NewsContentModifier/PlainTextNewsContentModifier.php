<?php

declare(strict_types=1);

namespace App\Application\NewsContentModifier;

readonly class PlainTextNewsContentModifier implements NewsContentModifierInterface
{
    public function __construct(
        private NewsContentModifierInterface $newsContentModifier,
    )
    {
    }

    public function modify(string $content): string
    {
        $content = $this->newsContentModifier->modify($content);

        return strip_tags($content);
    }
}
