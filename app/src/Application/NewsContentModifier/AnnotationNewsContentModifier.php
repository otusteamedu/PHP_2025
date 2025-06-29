<?php

declare(strict_types=1);

namespace App\Application\NewsContentModifier;

readonly class AnnotationNewsContentModifier implements NewsContentModifierInterface
{
    public function __construct(
        private NewsContentModifierInterface $newsContentModifier,
    )
    {
    }

    public function modify(string $content): string
    {
        $content = $this->newsContentModifier->modify($content);

        return mb_substr($content, 0, 100) . '...';
    }
}
