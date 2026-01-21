<?php

namespace App\ES\Config;

class BaseConfiguration
{
    public function getIndexName(): string
    {
        return $_ENV['PRODUCT_INDEX'];
    }

    public function getMapping(): array
    {
        return include __DIR__.'/product.mapping.php';
    }

    public function getSettings(): array
    {
        return include __DIR__.'/product.settings.php';
    }
}