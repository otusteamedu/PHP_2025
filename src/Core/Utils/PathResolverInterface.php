<?php

declare(strict_types=1);

namespace App\Core\Utils;

interface PathResolverInterface
{
    public function getProjectRoot(): string;
    public function getSrcPath(): string;
    public function getVarPath(): string;
    public function getConfigPath(): string;
    public function getTemplatesPath(): string;
    public function build(string $relativePath): string;
}
