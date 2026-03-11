<?php

namespace Pryaniki\App\Application\Services;

interface ProductImporterInterface
{
    public function import(string $filePath): void;
}