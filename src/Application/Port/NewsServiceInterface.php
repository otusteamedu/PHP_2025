<?php

namespace App\Application\Port;

interface NewsServiceInterface
{
    public function getHtmlByUrl(string $url, string $tag = '');
}