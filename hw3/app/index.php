<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Otus\UrlHelper\UrlHelper;

$helper = new UrlHelper();

$testUrls = [
    'https://example.com' => true,
    'http://example.com' => false,
    'https://google.com' => true,
    'ftp://example.com' => false,
];

foreach ($testUrls as $url => $expected) {
    $result = $helper->isHttps($url);
    $status = $result === $expected ? '✓' : '✗';
    echo "$status $url - HTTPS: " . ($result ? 'YES' : 'NO') . "\n";
}
