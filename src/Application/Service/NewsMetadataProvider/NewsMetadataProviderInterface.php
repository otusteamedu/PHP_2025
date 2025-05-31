<?php

namespace App\Application\Service\NewsMetadataProvider;

use App\Domain\ValueObject\Url;

interface NewsMetadataProviderInterface
{
    public function fetchTitle(string $url): string;
}
