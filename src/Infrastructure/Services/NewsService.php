<?php

declare(strict_types=1);

namespace App\Infrastructure\Services;

class NewsService
{
    public function saveDownloadNewsAsHtml(string $url)
    {
        $homepage = file_get_contents($url);
        dd($homepage);
    }
}
