<?php declare(strict_types=1);

namespace App\Application\Service;

interface NewsMetadataProviderInterface
{
    public function fetchTitle(string $url): string;
}
