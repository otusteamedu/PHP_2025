<?php declare(strict_types=1);

namespace App\Application\Service\NewsMetadataProvider;

readonly class NewsMetadataProviderResponse
{
    public function __construct(
        public string $title
    )
    {
    }
}
