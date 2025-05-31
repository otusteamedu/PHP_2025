<?php

namespace App\Application\Service\NewsMetadataProvider;

interface NewsMetadataProviderInterface
{
    public function fetchTitle(string $url): string;
}
