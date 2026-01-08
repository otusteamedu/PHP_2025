<?php

namespace Otus\UrlHelper;

class UrlHelper
{
    /**
     * Проверяет, использует ли URL протокол HTTPS
     */
    public function isHttps(string $url): bool
    {
        $parsedUrl = parse_url($url);
        return isset($parsedUrl['scheme']) && $parsedUrl['scheme'] === 'https';
    }
}