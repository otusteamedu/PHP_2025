<?php declare(strict_types=1);

namespace App\Application\Service\NewsMetadataProvider;

interface NewsMetadataProviderInterface
{
    public function fetchTitle(NewsMetadataProviderRequest $request): NewsMetadataProviderResponse;
}
