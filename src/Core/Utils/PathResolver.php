<?php

declare(strict_types=1);

namespace App\Core\Utils;

class PathResolver implements PathResolverInterface
{
    private readonly string $projectRoot;

    public function __construct()
    {
        $this->projectRoot = $this->findProjectRoot(__DIR__);
    }

    public function getProjectRoot(): string
    {
        return $this->projectRoot;
    }

    public function getSrcPath(): string
    {
        return $this->getProjectRoot() . '/src';
    }

    public function getVarPath(): string
    {
        return $this->getProjectRoot() . '/var';
    }

    public function getConfigPath(): string
    {
        return $this->getProjectRoot() . '/config';
    }

    public function getTemplatesPath(): string
    {
        return $this->getProjectRoot() . '/templates';
    }

    public function build(string $relativePath): string
    {
        return $this->getProjectRoot()  . '/' . ltrim($relativePath, '/');
    }

    private function findProjectRoot(string $startDir): string
    {
        $dir = realpath($startDir);
        while (strlen($dir) > 1) {
            if (is_file($dir . '/composer.json')) {
                return $dir;
            }
            $dir = dirname($dir);
        }

        throw new \RuntimeException('Project root (composer.json) not found.');
    }
}
