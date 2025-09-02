<?php

namespace App\Index;

class IndexConfigFactory
{
    public static function create(string $type, array $options = []): IndexConfigInterface
    {
        switch ($type) {
            case 'default':
                return new DefaultIndexConfig($options['name'] ?? 'otus-shop');
            case 'custom':
                return new CustomIndexConfig(
                    $options['name'] ?? 'otus-shop',
                    $options['params'] ?? []
                );
            default:
                throw new \InvalidArgumentException("Unknown index config type: $type");
        }
    }
}