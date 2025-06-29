<?php

declare(strict_types=1);

namespace App\Application\NewsContentModifier;

readonly class SocialNetworksNewsContentModifier implements NewsContentModifierInterface
{
    public function __construct(
        private NewsContentModifierInterface $newsContentModifier,
    )
    {
    }

    public function modify(string $content): string
    {
        $content = $this->newsContentModifier->modify($content);

        return $content . $this->renderSocialNetworks();
    }

    private function renderSocialNetworks(): string
    {
        $socialNetworks = [];
        $socialNetworks[] = '<p>';
        $socialNetworks[] = '<a href="#">VK</a>';
        $socialNetworks[] = '<a href="#">Telegram</a>';
        $socialNetworks[] = '<a href="#">Whatsapp</a>';
        $socialNetworks[] = '</p>';

        return implode('', $socialNetworks);
    }
}
