<?php

declare(strict_types=1);

namespace App\Core\Utils;

class PathResolver
{
    private static ?string $root = null;

    public static function setRoot(string $path): void
    {
        self::$root = $path;
    }

    public static function getRoot(): string
    {
        if (self::$root === null) {
            throw new \RuntimeException('PathResolver::setRoot() must be called before getRoot().');
        }

        return self::$root;
    }

    public static function getSrcPath(): string
    {
        return self::getRoot() . '/src';
    }

    public static function getVarPath(): string
    {
        return self::getRoot() . '/var';
    }

    public static function build(string $relativePath): string
    {
        return self::getRoot() . '/' . ltrim($relativePath, '/');
    }
}
